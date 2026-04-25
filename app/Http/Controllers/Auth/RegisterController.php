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

        // 2. Crear el usuario vinculado a la persona
        $usuario = Usuario::create([
            'persona_id' => $persona->id,
            'email'      => $request->email,
            'contrasena' => Hash::make($request->password),
            'estado'     => 'ACTIVO',
        ]);

        // 3. Crear el cliente vinculado al usuario
        Cliente::create([
            'usuario_id' => $usuario->id,
        ]);

        event(new Registered($usuario));

        Auth::login($usuario);

        return redirect()->route('cliente.dashboard');
    }
}
