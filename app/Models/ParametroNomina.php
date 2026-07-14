<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use RuntimeException;

class ParametroNomina extends Model
{
    protected $table = 'parametros_nomina';

    protected $fillable = [
        'clave',
        'valor',
        'vigente_desde',
        'vigente_hasta',
        'fuente',
    ];

    protected $casts = [
        'valor'         => 'decimal:4',
        'vigente_desde' => 'date',
        'vigente_hasta' => 'date',
    ];

    /**
     * Devuelve el valor vigente de una clave para una fecha dada.
     * Lanza una excepción si no hay ningún parámetro vigente configurado,
     * en vez de devolver 0 silenciosamente (un 0 mal calculado en nómina
     * es peor que un error explícito).
     */
    public static function valorVigente(string $clave, ?Carbon $fecha = null): float
    {
        $fecha = $fecha ?? now();

        $parametro = static::where('clave', $clave)
            ->where('vigente_desde', '<=', $fecha)
            ->where(function ($q) use ($fecha) {
                $q->whereNull('vigente_hasta')->orWhere('vigente_hasta', '>=', $fecha);
            })
            ->orderByDesc('vigente_desde')
            ->first();

        if (!$parametro) {
            throw new RuntimeException("No hay un parámetro de nómina vigente para la clave '{$clave}' en la fecha {$fecha->toDateString()}.");
        }

        return (float) $parametro->valor;
    }
}
