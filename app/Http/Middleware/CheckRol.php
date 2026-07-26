<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Uso en rutas:
 *   ->middleware('rol:ADMINISTRADOR')
 *   ->middleware('rol:FOTOGRAFO')
 *   ->middleware('rol:CLIENTE')
 *   ->middleware('rol:ADMINISTRADOR,FOTOGRAFO')  // múltiples roles permitidos
 */
class CheckRol
{
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $usuario = Auth::user();

        if (! $usuario || empty(array_intersect($usuario->getRoles(), $roles))) {
            abort(403, 'No tienes permiso para acceder a esta sección.');
        }

        return $next($request);
    }
}

