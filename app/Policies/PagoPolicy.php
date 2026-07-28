<?php

namespace App\Policies;

use App\Models\Pago;
use App\Models\Usuario;

class PagoPolicy
{
    /**
     * El cliente solo puede ver/gestionar sus propios pagos.
     * (misma regla que antes vivía en ClientePagoController::autorizarPropietario)
     */
    public function verComoCliente(Usuario $usuario, Pago $pago): bool
    {
        return $pago->cliente_id === $usuario->cliente?->id;
    }
}
