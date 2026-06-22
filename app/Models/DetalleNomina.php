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
        'sueldo_neto',
    ];

    protected $casts = [
        'salario_bruto'      => 'decimal:2',
        'descuentos_legales' => 'decimal:2',
        'sueldo_neto'        => 'decimal:2',
    ];

    public function nomina(): BelongsTo
    {
        return $this->belongsTo(Nomina::class, 'nomina_id');
    }

    public function fotografo(): BelongsTo
    {
        return $this->belongsTo(Fotografo::class, 'fotografo_id');
    }
}
