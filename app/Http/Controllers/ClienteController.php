<?php

namespace App\Http\Controllers;

use App\Services\ClienteService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ClienteController extends Controller
{
    public function __construct(
        private ClienteService $clienteService
    ) {}

    public function dashboard()
    {
        $cliente  = Auth::user()->cliente;
        $metricas = $this->clienteService->metricasDashboard($cliente);

        return view('cliente.dashboard', $metricas);
    }

    public function perfil()
    {
        $usuario        = Auth::user()->load('persona');
        $sesionesporMes = $this->clienteService->sesionesPorMes($usuario->cliente);

        return view('cliente.perfil', compact('usuario', 'sesionesporMes'));
    }

    public function galeria()
    {
        return view('cliente.galeria');
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user()->load('persona');

        $request->validate([
            'nombre'   => 'required|string|max:100',
            'apellido' => 'required|string|max:100',
            'telefono' => 'nullable|string|max:20',
            'email'    => 'required|email|max:150|unique:usuarios,email,' . $usuario->id,
        ]);

        $usuario->persona->update([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        $usuario->update(['email' => $request->email]);

        return redirect()->route('cliente.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }
}
