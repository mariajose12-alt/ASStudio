<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reembolso extends Model
{
    protected $table = 'reembolsos';

    protected $fillable = [
        'pago_original_id',
        'monto',
        'motivo',
    ];

    protected $casts = [
        'monto'           => 'decimal:2',
        'fecha_procesado' => 'date',
    ];

    public function pagoOriginal(): BelongsTo
    {
        return $this->belongsTo(Pago::class, 'pago_original_id');
    }

    public function estaPendiente(): bool
    {
        return $this->estado === 'PENDIENTE';
    }

    public function estaProcesado(): bool
    {
        return $this->estado === 'PROCESADO';
    }

    public function estaRechazado(): bool
    {
        return $this->estado === 'RECHAZADO';
    }
    public function marcarProcesado(): void
    {
        $this->estado = 'PROCESADO';
        $this->fecha_procesado = now();
        $this->save();
    }

    public function marcarRechazado(): void
    {
        $this->estado = 'RECHAZADO';
        $this->save();
    }
}
