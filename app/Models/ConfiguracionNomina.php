<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ConfiguracionNomina extends Model
{
    protected $table = 'configuracion_nomina';

    protected $fillable = [
        'salario_base_default',
        'incentivos_activos',
        'moneda_display',
        'actualizado_por_id',
    ];

    protected $casts = [
        'incentivos_activos' => 'boolean',
    ];

    public function actualizadoPor(): BelongsTo
    {
        return $this->belongsTo(Administrador::class, 'actualizado_por_id');
    }

    public static function actual(): self
    {
        return self::query()->first() ?? self::create(
            [
                'salario_base_default' => 10000.00,
                'incentivos_activos'   => false,
                'moneda_display'       => 'RD$',
            ]
        );
    }
}
