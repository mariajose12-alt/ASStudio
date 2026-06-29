<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Nomina extends Model
{
    protected $table = 'nominas';

    protected $fillable = [
        'creada_por_id',
        'periodo',
        'fecha_inicio',
        'fecha_fin',
        'total_salarios_brutos',
        'total_descuentos_legales',
        'total_isr_retenido',
        'total_aportes_patronales',
        'total_nomina_neta',
        'estado',
    ];

    protected $casts = [
        'fecha_inicio'             => 'date',
        'fecha_fin'                => 'date',
        'total_salarios_brutos'    => 'decimal:2',
        'total_descuentos_legales' => 'decimal:2',
        'total_isr_retenido'       => 'decimal:2',
        'total_aportes_patronales' => 'decimal:2',
        'total_nomina_neta'        => 'decimal:2',
    ];

    public function creadaPor(): BelongsTo
    {
        return $this->belongsTo(Administrador::class, 'creada_por_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(DetalleNomina::class, 'nomina_id');
    }

    public function estaCalculada(): bool
    {
        return $this->estado === 'CALCULADA';
    }

    public function estaPagada(): bool
    {
        return $this->estado === 'PAGADA';
    }

    public function estaCerrada(): bool
    {
        return $this->estado === 'CERRADA';
    }

    public function todosFotografosConfirmaron(): bool
    {
        return $this->detalles->isNotEmpty() &&
            $this->detalles->every(fn($detalle) => $detalle->estaConfirmado());
    }

    public function tieneDisputasPendientes(): bool
    {
        return $this->detalles->contains(fn($detalle) => $detalle->estaEnDisputa());
    }

    public function detallesPendientesDeRevision(): int
    {
        return $this->detalles->where('estado_confirmacion', 'PENDIENTE')->count();
    }
}
