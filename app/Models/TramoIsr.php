<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class TramoIsr extends Model
{
    protected $table = 'tramos_isr';

    protected $fillable = [
        'orden',
        'desde_anual',
        'hasta_anual',
        'tasa',
        'monto_fijo_adicional',
        'vigente_desde',
        'vigente_hasta',
        'fuente',
    ];

    protected $casts = [
        'desde_anual'          => 'decimal:2',
        'hasta_anual'          => 'decimal:2',
        'tasa'                 => 'decimal:4',
        'monto_fijo_adicional' => 'decimal:2',
        'vigente_desde'        => 'date',
        'vigente_hasta'        => 'date',
    ];

    /**
     * Devuelve el tramo que aplica a un ingreso anualizado, según la escala
     * vigente en la fecha dada. Lanza una excepción si no hay ninguna escala
     * configurada para esa fecha, en vez de dejar pasar el ISR como 0
     * silenciosamente.
     */
    public static function paraMonto(float $montoAnualizado, ?Carbon $fecha = null): self
    {
        $fecha = $fecha ?? now();

        $tramo = static::where('vigente_desde', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->whereNull('vigente_hasta')->orWhere('vigente_hasta', '>=', $fecha);
            })
            ->where('desde_anual', '<=', $montoAnualizado)
            ->where(function ($q) use ($montoAnualizado) {
                $q->whereNull('hasta_anual')->orWhere('hasta_anual', '>=', $montoAnualizado);
            })
            ->orderByDesc('vigente_desde')
            ->orderBy('orden')
            ->first();

        if (!$tramo) {
            throw new \RuntimeException("No hay un tramo de ISR vigente para un monto anualizado de {$montoAnualizado} en la fecha {$fecha->toDateString()}.");
        }

        return $tramo;
    }
}
