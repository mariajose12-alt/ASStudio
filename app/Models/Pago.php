<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Pago extends Model
{
    protected $table = 'pagos';

    protected $fillable = [
        'reserva_id',
        'cliente_id',
        'comprobante_id',
        'monto',
        'fecha_registro',
        'fecha_completado',
        'estado',
        'metodo',
        'tipo',
        'motivo_rechazo',
    ];

    protected $casts = [
        'monto'            => 'decimal:2',
        'fecha_registro'   => 'datetime',
        'fecha_completado' => 'datetime',
    ];

    public function reserva(): BelongsTo
    {
        return $this->belongsTo(Reserva::class, 'reserva_id');
    }

    public function cliente(): BelongsTo
    {
        return $this->belongsTo(Cliente::class, 'cliente_id');
    }

    public function comprobante(): BelongsTo
    {
        return $this->belongsTo(Comprobante::class, 'comprobante_id');
    }

    public function estaConfirmado(): bool
    {
        return $this->estado === 'CONFIRMADO';
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    public function estaEnRevision(): bool
    {
        return $this->estado === 'EN_REVISION';
    }

    public function esAnticipo(): bool
    {
        return $this->tipo === 'ANTICIPO';
    }

    public function esPagoFinal(): bool
    {
        return $this->tipo === 'FINAL';
    }

    public function aprobar(): void
    {
        $this->update([
            'estado'           => 'CONFIRMADO',
            'fecha_completado' => now(),
        ]);
    }

    public function rechazar(string $motivo): void
    {
        $this->update([
            'estado'          => 'RECHAZADO',
            'motivo_rechazo'  => $motivo,
        ]);
    }

    public function asociarComprobante(Comprobante $comprobante): void
    {
        $this->update(['comprobante_id' => $comprobante->id]);
    }
}
