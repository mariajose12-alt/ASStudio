<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DetalleNomina extends Model
{
    protected $table = 'detalles_nomina';

    protected $fillable = [
        'nomina_id',
        'fotografo_id',
        'salario_bruto',
        'descuentos_legales',
        'descuento_tss',
        'descuento_isr',
        'dependientes_adicionales_aplicados',
        'monto_dependiente_unitario_usado',
        'descuento_dependientes',
        'sueldo_neto',
        'estado_confirmacion',
        'confirmado_at',
        'observacion_fotografo',

    ];

    protected $casts = [
        'salario_bruto'      => 'decimal:2',
        'descuentos_legales' => 'decimal:2',
        'descuento_tss'      => 'decimal:2',
        'descuento_isr'      => 'decimal:2',
        'monto_dependiente_unitario_usado' => 'decimal:2',
        'descuento_dependientes'           => 'decimal:2',
        'sueldo_neto'        => 'decimal:2',
        'confirmado_at'      => 'datetime',
    ];

    public function nomina(): BelongsTo
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }

    public function estaPendiente(): bool
    {
        return $this->estado_confirmacion === 'PENDIENTE';
    }

    public function estaConfirmado(): bool
    {
        return $this->estado_confirmacion === 'CONFIRMADO';
    }

    public function estaEnDisputa(): bool
    {
        return $this->estado_confirmacion === 'EN_DISPUTA';
    }
}
