<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    public function showForm()
    {
        if (Auth::check()) {
            /** @var Usuario $usuario */
            $usuario = Auth::user();
            return $this->redirigirPorRol($usuario);
        }

        return view('auth.login');
    }

    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $attempt = Auth::attempt([
            'email'    => $credentials['email'],
            'password' => $credentials['password'],
        ], $request->boolean('remember'));

        if (! $attempt) {
            throw ValidationException::withMessages([
                'email' => __('Las credenciales no coinciden con nuestros registros.'),
            ]);
        }

        /** @var Usuario $usuario */
        $usuario = Auth::user();

        if (! $usuario->estaActivo()) {
            Auth::logout();
            throw ValidationException::withMessages([
                'email' => __('Tu cuenta está inactiva. Contacta al administrador.'),
            ]);
        }

        $request->session()->regenerate();

        return $this->redirigirPorRol($usuario);
    }

    public function logout(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    private function redirigirPorRol(Usuario $usuario): RedirectResponse
    {
        return match ($usuario->getRol()) {
            'ADMINISTRADOR' => redirect()->route('admin.dashboard'),
            'FOTOGRAFO'     => redirect()->route('fotografo.dashboard'),
            'CLIENTE'       => redirect()->route('cliente.dashboard'),
            default         => redirect('/'),
        };
    }
}
