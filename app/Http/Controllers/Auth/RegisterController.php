<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Cliente;
use App\Models\Persona;
use App\Models\Usuario;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;
use Illuminate\Support\Str;
use App\Mail\VerificacionEmail;
use App\Mail\BienvenidaEmail;
use Illuminate\Support\Facades\Mail;

class RegisterController extends Controller
{
    public function showForm(): View
    {
        return view('auth.register');
    }

    public function register(Request $request): RedirectResponse
    {
        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email'    => ['required', 'string', 'lowercase', 'email', 'max:150', 'unique:usuarios,email'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        // 1. Crear la persona
        $persona = Persona::create([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        $token = Str::random(64);

        // 2. Crear el usuario vinculado a la persona
        $usuario = Usuario::create([
            'persona_id' => $persona->id,
            'email'      => $request->email,
            'contrasena' => Hash::make($request->password),
        ]);
        $usuario->establecerEstado('ACTIVO');
        $usuario->asignarTokenVerificacion($token);

        // 3. Crear el cliente vinculado al usuario
        Cliente::create([
            'usuario_id' => $usuario->id,
        ]);

        // Enviar correo de verificación
        Mail::to($usuario->email)->send(new VerificacionEmail($usuario));

        // Iniciar sesión automáticamente para llevarlo directo a la pantalla de espera
        Auth::login($usuario);

        return redirect()->route('cliente.verificacion.pendiente');
    }

    // Valida que la URL de redirect sea interna y esté en la whitelist.
    private function esRedirectSeguro(string $url): bool
    {
        if (! str_starts_with($url, '/')) {
            return false;
        }

        $permitidas = [
            '/cliente/reservas/paso1',
            '/cliente/reservas/paso2',
            '/cliente/reservas/paso3',
            '/cliente/reservas',
            '/cliente/dashboard',
            '/catalogo',
        ];

        return in_array($url, $permitidas);
    }

    public function verificar(string $token): RedirectResponse
    {
        $usuario = Usuario::where('token_verificacion', $token)->first();

        if (!$usuario) {
            return redirect()->route('login')->with('error', 'El enlace de verificación no es válido o ya fue usado.');
        }

        $usuario->marcarVerificado();

        Mail::to($usuario->email)->send(new BienvenidaEmail($usuario));

        Auth::login($usuario);

        return redirect()->route('cliente.dashboard')->with('success', '¡Cuenta verificada! Bienvenido a AS Studio.');
    }

    public function reenviarVerificacion(): RedirectResponse
    {
        $usuario = auth()->user();

        if ($usuario->estaVerificado()) {
            return redirect()->route('cliente.dashboard');
        }

        $token = Str::random(64);
        $usuario->asignarTokenVerificacion($token);

        Mail::to($usuario->email)->send(new VerificacionEmail($usuario));

        return back()->with('success', '¡Correo reenviado! Revisa tu bandeja de entrada.');
    }

}
