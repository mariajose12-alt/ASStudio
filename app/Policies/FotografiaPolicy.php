<?php

namespace App\Policies;

use App\Models\Fotografia;
use App\Models\Usuario;

class FotografiaPolicy
{
    /**
     * Mismo criterio que SesionPolicy::gestionar, aplicado vía la sesión
     * a la que pertenece la foto.
     */
    public function gestionar(Usuario $usuario, Fotografia $fotografia): bool
    {
        $fotografo = $usuario->empleado?->fotografo;

        return $fotografo && $fotografia->sesion->fotografoTieneAcceso($fotografo);
    }

    /**
     * Mismo criterio que SesionPolicy::esPrincipal, aplicado vía la sesión
     * a la que pertenece la foto.
     */
    public function esPrincipal(Usuario $usuario, Fotografia $fotografia): bool
    {
        $fotografo = $usuario->empleado?->fotografo;

        return $fotografo && $fotografia->sesion->esPrincipalDe($fotografo);
    }
}
