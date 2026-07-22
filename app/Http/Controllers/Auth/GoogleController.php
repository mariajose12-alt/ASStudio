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
                'verificado' => true,
                'token_verificacion' => null,
            ]);

            // Crear cliente por defecto
            Cliente::create(['usuario_id' => $usuario->id]);
        }

        // Auth::login necesita un modelo que extienda Authenticatable
        // asegúrate que tu modelo Usuario extienda Authenticatable
        Auth::login($usuario);

        return redirect('/');
    }
}
