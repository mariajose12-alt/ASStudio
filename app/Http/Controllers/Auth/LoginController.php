<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    // Mostrar formulario de login.
    public function showForm()
    {
        return view('auth.login');
    }

    // Procesar login.
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'     => ['required', 'email'],
            'password'  => ['required', 'string'],
        ]);

        $attempt = Auth::attempt([
            'email'     => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'));

        if (! $attempt) {
            throw ValidationException::withMessages([
                'email' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        /** @var \App\Models\Usuario $usuario */
        $usuario = Auth::user();

        // Bloquear usuarios inactivos
        if (! $usuario->estaActivo()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Tu cuenta está inactiva. Contacta al administrador.'),
            ]);
        }

        $request->session()->regenerate();

        return $this->redirigirPorRol($usuario);
    }

    //Cerrar sesión.
    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    //Redirige al dashboard correspondiente según el rol del usuario.
    private function redirigirPorRol(\App\Models\Usuario $usuario): RedirectResponse
    {
        return match ($usuario->getRol()) {
            'ADMINISTRADOR' => redirect()->route('admin.dashboard'),
            'FOTOGRAFO'     => redirect()->route('fotografo.dashboard'),
            'CLIENTE'       => redirect()->route('cliente.dashboard'),
            default         => redirect('/'),
        };
    }
}
