<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Comprobante extends Model
{
    use HasFactory;

    protected $table = 'comprobantes';

    protected $fillable = [
        'archivo_key',
        'monto_detectado',
        'fecha_detectada',
        'banco_detectado',
        'referencia_detectada',
        'estado_ocr',
        'respuesta_ocr_raw',
    ];

    protected $casts = [
        'monto_detectado'   => 'decimal:2',
        'fecha_detectada'   => 'date',
        'respuesta_ocr_raw' => 'array',
        'created_at'        => 'datetime',
        'updated_at'        => 'datetime',
    ];

    // ── Relaciones ──────────────────────────────────────────────

    public function pago(): BelongsTo
    {
        return $this->belongsTo(Pago::class);
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
