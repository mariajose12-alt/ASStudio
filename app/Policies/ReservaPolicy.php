<?php

namespace App\Policies;

use App\Models\Reserva;
use App\Models\Usuario;

class ReservaPolicy
{
    /**
     * El cliente solo puede ver/gestionar sus propias reservas.
     * (misma regla que antes vivía en ClienteReservaController::autorizarPropietario)
     */
    public function verComoCliente(Usuario $usuario, Reserva $reserva): bool
    {
        return $reserva->cliente_id === $usuario->cliente?->id;
    }

    /**
     * El fotógrafo puede gestionar las reservas que ya son suyas, o las que
     * todavía están PENDIENTE sin fotógrafo asignado (disponibles para tomar).
     * (misma regla que antes vivía en FotografoReservaController::autorizarFotografo)
     */
    public function verComoFotografo(Usuario $usuario, Reserva $reserva): bool
    {
        $fotografo = $usuario->empleado?->fotografo;

        if (! $fotografo) {
            return false;
        }

        $esSuya       = $reserva->fotografo_id === $fotografo->id;
        $esDisponible = is_null($reserva->fotografo_id) && $reserva->estado === 'PENDIENTE';

        return $esSuya || $esDisponible;
    }
}
