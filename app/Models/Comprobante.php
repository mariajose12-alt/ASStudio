<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobantes';

    protected $fillable = [
        'archivo_key',
        'fecha_detectada',
    ];

    protected $casts = [
        'monto_detectado'   => 'decimal:2',
        'fecha_detectada'   => 'date',
        'respuesta_ocr_raw' => 'array',
        'cuenta_destino_valida' => 'boolean',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────

    /**
     * La FK vive en la tabla `pagos` (pagos.comprobante_id), no en `comprobantes`.
     * Por eso es hasOne y no belongsTo.
     */
    public function pago(): HasOne
    {
        return $this->hasOne(Pago::class);
    }

    // ── Helpers ─────────────────────────────────────────────────

    /**
     * URL firmada temporal para visualizar el comprobante desde R2.
     */
    public function urlFirmada(int $minutos = 15): string
    {
        return \Storage::disk('r2')->temporaryUrl(
            $this->archivo_key,
            now()->addMinutes($minutos)
        );
    }

    /**
     * Compara el monto detectado por OCR contra el monto esperado del pago.
     * Devuelve null si el OCR aún no procesó o falló.
     */
    public function montoCoincideCon(float $montoEsperado, float $tolerancia = 0.01): ?bool
    {
        if ($this->monto_detectado === null) {
            return null;
        }

        return abs((float) $this->monto_detectado - $montoEsperado) <= $tolerancia;
    }

    public function marcarProcesado(array $datos): void
    {
        $this->monto_detectado          = $datos['monto']             ?? null;
        $this->fecha_detectada          = $datos['fecha']              ?? null;
        $this->banco_detectado          = $datos['banco']              ?? null;
        $this->referencia_detectada     = $datos['referencia']         ?? null;
        $this->cuenta_destino_detectada = $datos['cuentaDestino']      ?? null;
        $this->cuenta_destino_valida    = $datos['cuentaValida']       ?? null;
        $this->fecha_comprobante_valida = $datos['fechaValida']        ?? null;
        $this->estado_ocr               = 'PROCESADO';
        $this->respuesta_ocr_raw        = ['texto_completo' => $datos['textoCompleto'] ?? null];
        $this->save();
    }

    public function marcarFallido(?string $motivo = null): void
    {
        $this->estado_ocr = 'FALLIDO';
        if ($motivo) {
            $this->respuesta_ocr_raw = ['error' => $motivo];
        }
        $this->save();
    }

    // ── Scopes ──────────────────────────────────────────────────

    public function scopePendientes($query)
    {
        return $query->where('estado_ocr', 'PENDIENTE');
    }

    public function scopeProcesados($query)
    {
        return $query->where('estado_ocr', 'PROCESADO');
    }

    public function scopeFallidos($query)
    {
        return $query->where('estado_ocr', 'FALLIDO');
    }
}
