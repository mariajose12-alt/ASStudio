<?php

namespace App\Jobs;

use App\Mail\ComprobantePendienteAdmin;
use App\Models\Comprobante;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use thiagoalessio\TesseractOCR\TesseractOCR;

class ProcesarComprobanteOcrJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries = 3;
    public int $backoff = 10;

    public function __construct(public Comprobante $comprobante)
    {
    }

    public function handle(): void
    {
        $rutaTemporal = null;

        try {
            // Descargar archivo de R2 a un temporal local
            $contenido = Storage::disk('r2')->get($this->comprobante->archivo_key);
            $rutaTemporal = tempnam(sys_get_temp_dir(), 'comprobante_') . '.jpg';
            file_put_contents($rutaTemporal, $contenido);

            $textoDetectado = (new TesseractOCR($rutaTemporal))
                ->tessdataDir('C:\Program Files\Tesseract-OCR\tessdata')
                ->lang('spa', 'eng') // fallback a inglés si spa falla
                ->timeout(30)        // máximo 30 segundos
                ->run();

            if (blank($textoDetectado)) {
                $this->comprobante->update([
                    'estado_ocr'        => 'FALLIDO',
                    'respuesta_ocr_raw' => ['error' => 'Tesseract no detectó texto'],
                ]);
                $this->notificarAdmins();
                return;
            }

            $datos = $this->extraerDatos($textoDetectado);

            $this->comprobante->update([
                'monto_detectado'      => $datos['monto'],
                'fecha_detectada'      => $datos['fecha'],
                'banco_detectado'      => $datos['banco'],
                'referencia_detectada' => $datos['referencia'],
                'estado_ocr'           => 'PROCESADO',
                'respuesta_ocr_raw'    => ['texto_completo' => $textoDetectado],
            ]);

            $this->notificarAdmins();

        } catch (\Throwable $e) {
            Log::error('Error procesando OCR de comprobante', [
                'comprobante_id' => $this->comprobante->id,
                'error'          => $e->getMessage(),
            ]);

            $this->comprobante->update(['estado_ocr' => 'FALLIDO']);
            $this->notificarAdmins();

        } finally {
            // Siempre limpiar el archivo temporal, pase lo que pase
            if ($rutaTemporal && file_exists($rutaTemporal)) {
                unlink($rutaTemporal);
            }
        }
    }

    private function notificarAdmins(): void
    {
        $pago = $this->comprobante->pago;

        if (!$pago) {
            return;
        }

        $reserva = $pago->reserva;

        if (!$reserva) {
            Log::warning('Pago sin reserva asociada al notificar admins', [
                'comprobante_id' => $this->comprobante->id,
                'pago_id'        => $pago->id,
            ]);
            return;
        }

        try {
            \App\Models\Usuario::administradores()->each(function ($admin) use ($reserva) {
                if ($admin->email) {
                    Mail::to($admin->email)->send(new ComprobantePendienteAdmin($reserva));
                }
            });
        } catch (\Throwable $e) {
            Log::error('Error notificando admins de comprobante pendiente', [
                'comprobante_id' => $this->comprobante->id,
                'error'          => $e->getMessage(),
            ]);
        }
    }

    private function extraerDatos(string $texto): array
    {
        $monto     = null;
        $fecha     = null;
        $banco     = null;
        $referencia = null;

        // Monto: "RD$500.00", "RD$ 1,500.00", "DOP 1500", "$1,500.00"
        if (preg_match('/RD\$\s*([\d,]+\.?\d{0,2})/i', $texto, $m)) {
            $monto = (float) str_replace(',', '', $m[1]);
        } elseif (preg_match('/(?:DOP|US\$|\$)\s*([\d,]+\.?\d{0,2})/i', $texto, $m)) {
            $monto = (float) str_replace(',', '', $m[1]);
        }

        // Fecha: "14 mar 2026", "14/03/2026", "14-03-2026"
        $meses = [
            'ene'=>'01','feb'=>'02','mar'=>'03','abr'=>'04','may'=>'05','jun'=>'06',
            'jul'=>'07','ago'=>'08','sep'=>'09','oct'=>'10','nov'=>'11','dic'=>'12',
        ];

        if (preg_match('/(\d{1,2})\s+(ene|feb|mar|abr|may|jun|jul|ago|sep|oct|nov|dic)\s+(\d{4})/i', $texto, $m)) {
            $mes   = $meses[strtolower($m[2])];
            $fecha = "{$m[3]}-{$mes}-" . str_pad($m[1], 2, '0', STR_PAD_LEFT);
        } elseif (preg_match('/(\d{2})[\/\-](\d{2})[\/\-](\d{4})/', $texto, $m)) {
            $fecha = "{$m[3]}-{$m[2]}-{$m[1]}";
        }

        // Banco: lista extendida con variantes
        $bancosConocidos = [
            'Banco Popular'       => ['Banco Popular', 'Popular Dominicano', 'App Popular'],
            'Banreservas'         => ['Banreservas', 'Banco de Reservas'],
            'BHD'                 => ['BHD', 'BHD León'],
            'Scotiabank'          => ['Scotiabank'],
            'Banco Santa Cruz'    => ['Banco Santa Cruz', 'bsc.com.do'],
            'Promerica'           => ['Promerica'],
            'APAP'                => ['APAP'],
            'Banco López de Haro' => ['López de Haro'],
        ];

        foreach ($bancosConocidos as $nombre => $variantes) {
            foreach ($variantes as $variante) {
                if (stripos($texto, $variante) !== false) {
                    $banco = $nombre;
                    break 2;
                }
            }
        }

        // Referencia: "No. de referencia: 603140632", "Referencia: XXXXX", "MSG...", "Confirmación: XXXXX"
        if (preg_match('/No\.?\s*de\s*referencia\s*[:#]?\s*([A-Z0-9\-]{4,})/i', $texto, $m)) {
            $referencia = $m[1];
        } elseif (preg_match('/(?:referencia|confirmaci[oó]n|transacci[oó]n)\s*[:#]?\s*([A-Z0-9\-]{4,})/i', $texto, $m)) {
            $referencia = $m[1];
        } elseif (preg_match('/\b(MSG\d{10,})\b/', $texto, $m)) {
            $referencia = $m[1];
        }

        return compact('monto', 'fecha', 'banco', 'referencia');
    }
}
