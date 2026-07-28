<?php

namespace App\Policies;

use App\Models\Sesion;
use App\Models\Usuario;

class SesionPolicy
{
    /**
     * El fotógrafo principal o cualquier asistente activo tiene acceso
     * a la sesión (verla, subir fotos, etc).
     * (misma regla que ya vivía en Sesion::fotografoTieneAcceso)
     */
    public function gestionar(Usuario $usuario, Sesion $sesion): bool
    {
        $fotografo = $usuario->empleado?->fotografo;

        return $fotografo && $sesion->fotografoTieneAcceso($fotografo);
    }

    /**
     * Solo el fotógrafo principal (no un asistente) puede aprobar/rechazar
     * ediciones, marcar la galería disponible o marcar la sesión entregada.
     * (misma regla que ya vivía en Sesion::esPrincipalDe)
     */
    public function esPrincipal(Usuario $usuario, Sesion $sesion): bool
    {
        $fotografo = $usuario->empleado?->fotografo;

        return $fotografo && $sesion->esPrincipalDe($fotografo);
    }
}
