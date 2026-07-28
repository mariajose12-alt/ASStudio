<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Usuario;
use App\Models\Persona;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;

class GoogleController extends Controller
{
    // Redirige al usuario a Google
    public function redirect()
    {
        return Socialite::driver('google')->redirect();
    }

    // Google redirige de vuelta aquí
    public function callback()
    {
        $googleUser = Socialite::driver('google')->user();
        $usuario = Usuario::whereEmail($googleUser->getEmail())->first();

        if ($usuario && !$usuario->estaActivo()) {
            return redirect()->route('login')->with('error', 'Tu cuenta está desactivada. Contacta al administrador.');
        }

        // Cuenta nueva: segura crearla y loguear, Google ya confirmó el correo.
        if (!$usuario) {
            $persona = Persona::create([
                'nombre'   => $googleUser->user['given_name'] ?? $googleUser->getName(),
                'apellido' => $googleUser->user['family_name'] ?? '',
            ]);

            $usuario = Usuario::create([
                'persona_id' => $persona->id,
                'email'      => $googleUser->getEmail(),
                'contrasena' => null,
                'google_id'  => $googleUser->getId(),
                'avatar'     => $googleUser->getAvatar(),
            ]);
            $usuario->marcarVerificado();

            Cliente::create(['usuario_id' => $usuario->id]);

            Auth::login($usuario);
            return redirect('/');
        }

        // Ya vinculada a este mismo Google: login normal.
        if ($usuario->google_id === $googleUser->getId()) {
            Auth::login($usuario);
            return redirect('/');
        }

        // Existe con este correo pero SIN vincular a Google todavía.
        // Solo autovincular si ya está verificada (alguien probó control real
        // de esa bandeja de entrada). Si no, no logueamos automático.
        if ($usuario->estaVerificado()) {
            $usuario->update([
                'google_id' => $googleUser->getId(),
                'avatar'    => $usuario->avatar ?? $googleUser->getAvatar(),
            ]);
            Auth::login($usuario);
            return redirect('/');
        }

        return redirect()->route('login')->with(
            'error',
            'Ya existe una cuenta con este correo pendiente de verificación. Inicia sesión con tu contraseña para verificarla antes de usar Google.'
        );
    }
}
