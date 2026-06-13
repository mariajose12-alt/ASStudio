<?php

namespace App\Jobs;

use App\Events\ZipGaleriaListo;
use App\Models\Fotografia;
use App\Models\Sesion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class GenerarZipGaleria implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public int $tries   = 3;
    public int $timeout = 300;

    public function __construct(
        private readonly Sesion $sesion,
        private readonly string $tipo,
        private readonly array  $fotoIds,
    ) {}

    public function handle(): void
    {
        $fotos = Fotografia::whereIn('id', $this->fotoIds)->get();

        if ($fotos->isEmpty()) {
            Log::warning("GenerarZipGaleria: sin fotos para sesión {$this->sesion->id}");
            return;
        }

        $zipNombre = "fotos_{$this->tipo}_{$this->sesion->id}.zip";
        $zipPath   = storage_path("app/tmp/{$zipNombre}");

        $this->asegurarDirectorio($zipPath);

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new \RuntimeException("No se pudo crear el ZIP en: {$zipPath}");
        }

        foreach ($fotos as $foto) {
            $url       = Storage::disk('r2')->temporaryUrl($foto->url, now()->addMinutes(15));
            $contenido = Http::timeout(30)->get($url)->body();

            if (!empty($contenido)) {
                $zip->addFromString(basename($foto->url), $contenido);
            } else {
                Log::warning("GenerarZipGaleria: no se pudo descargar foto ID {$foto->id}");
            }
        }

        $zip->close();

        $destino = "zips/{$zipNombre}";
        Storage::disk('r2')->put($destino, file_get_contents($zipPath));
        @unlink($zipPath);

        $urlDescarga = Storage::disk('r2')->temporaryUrl($destino, now()->addHours(24));

        $usuario = $this->sesion->reserva->cliente->usuario;

        ZipGaleriaListo::dispatch($usuario, $this->sesion, $urlDescarga, $this->tipo);
    }

    public function failed(\Throwable $e): void
    {
        Log::error("GenerarZipGaleria falló para sesión {$this->sesion->id}: {$e->getMessage()}");
    }

    private function asegurarDirectorio(string $zipPath): void
    {
        $dir = dirname($zipPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }
    }
}
