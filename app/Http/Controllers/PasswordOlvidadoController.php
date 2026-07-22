<?php

namespace App\Http\Controllers;

use App\Mail\RestablecerPasswordCliente;
use App\Models\ActivacionCuenta;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class PasswordOlvidadoController extends Controller
{
    public function mostrarFormulario()
    {
        return view('auth.olvide-password');
    }

    public function enviarLink(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $usuario = Usuario::where('email', $request->email)->first();

        if ($usuario && $usuario->estaActivo()) {
            $tokenCrudo = ActivacionCuenta::generarPara($usuario);
            Mail::to($usuario->email)->send(new RestablecerPasswordCliente($usuario, $tokenCrudo));
        }

        // Mismo mensaje exista o no el usuario — evita enumeración de cuentas
        return back()->with('success', 'Si el correo está registrado, te enviamos un enlace para restablecer tu contraseña.');
    }
}
