<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckVerificado
{
    public function handle(Request $request, Closure $next): Response
    {
        /** @var \App\Models\Usuario|null $usuario */
        $usuario = Auth::user();

        if ($usuario && $usuario->getRol() === 'CLIENTE' && ! $usuario->estaVerificado()) {
            return redirect()->route('cliente.verificacion.pendiente');
        }

        return $next($request);
    }
}
