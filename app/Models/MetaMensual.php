<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MetaMensual extends Model
{
    protected $table = 'metas_mensuales';

    protected $fillable = ['mes', 'anio', 'ingresos', 'reservas', 'clientes_nuevos'];

    /**
     * Devuelve la meta del mes/año indicado.
     * Si no existe, cae de vuelta a la última meta definida
     * (sin guardarla) para que el dashboard no muestre 0% roto.
     */
    public static function paraMes(int $mes, int $anio): self
    {
        $meta = static::where('mes', $mes)->where('anio', $anio)->first();

        if ($meta) {
            return $meta;
        }

        $ultima = static::orderByDesc('anio')->orderByDesc('mes')->first();

        return new static([
            'mes'             => $mes,
            'anio'            => $anio,
            'ingresos'        => $ultima->ingresos ?? 0,
            'reservas'        => $ultima->reservas ?? 0,
            'clientes_nuevos' => $ultima->clientes_nuevos ?? 0,
        ]);
    }

    public static function existeParaMes(int $mes, int $anio): bool
    {
        return static::where('mes', $mes)->where('anio', $anio)->exists();
    }
}
