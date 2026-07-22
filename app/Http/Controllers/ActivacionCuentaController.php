<?php

namespace App\Http\Controllers;

use App\Models\ActivacionCuenta;
use App\Models\Usuario;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class ActivacionCuentaController extends Controller
{
    public function mostrar(Usuario $usuario, string $token)
    {
        $activacion = $this->buscarActivacionValida($usuario, $token);

        if (!$activacion) {
            return view('auth.activacion-invalida');
        }

        return view('auth.activar-cuenta', compact('usuario', 'token'));
    }

    public function procesar(Request $request, Usuario $usuario, string $token)
    {
        $activacion = $this->buscarActivacionValida($usuario, $token);

        if (!$activacion) {
            return redirect()
                ->route('activacion.mostrar', [$usuario, $token])
                ->with('error', 'Este enlace ya no es válido o expiró. Solicita uno nuevo al administrador.');
        }

        $request->validate([
            'password' => 'required|string|min:8|confirmed',
        ]);

        $usuario->update(['contrasena' => Hash::make($request->password)]);
        $activacion->update(['usado_en' => now()]);

        return redirect()
            ->route('login')
            ->with('success', 'Tu contraseña ha sido configurada. Ya puedes iniciar sesión.');
    }

    private function buscarActivacionValida(Usuario $usuario, string $tokenCrudo): ?ActivacionCuenta
    {
        return $usuario->activacionesCuenta()
            ->whereNull('usado_en')
            ->where('expira_en', '>', now())
            ->get()
            ->first(fn ($activacion) => Hash::check($tokenCrudo, $activacion->token));
    }
}
