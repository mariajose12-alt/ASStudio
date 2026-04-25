<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class ClienteController extends Controller
{
    public function dashboard()
    {
        $usuario = Auth::user();
        $cliente = $usuario->cliente;

        $totalReservas       = $cliente->reservas()->count();
        $reservasPendientes  = $cliente->reservas()->where('estado', 'PENDIENTE')->count();
        $reservasCompletadas = $cliente->reservas()->where('estado', 'COMPLETADA')->count();

        $proximasReservas = $cliente->reservas()
            ->with('paquete')
            ->where('fecha_inicio', '>=', now())
            ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
            ->orderBy('fecha_inicio')
            ->limit(5)
            ->get();

        return view('cliente.dashboard', compact(
            'totalReservas',
            'reservasPendientes',
            'reservasCompletadas',
            'proximasReservas',
        ));
    }

    public function perfil()
    {
        $usuario = Auth::user()->load('persona');

        // Sesiones completadas agrupadas por mes (últimos 6 meses)
        $sesionesporMes = $this->getSesionesPorMes($usuario->cliente);

        return view('cliente.perfil', compact('usuario', 'sesionesporMes'));
    }

    public function actualizarPerfil(Request $request)
    {
        $usuario = Auth::user()->load('persona');

        $request->validate([
            'nombre'   => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'email'    => ['required', 'string', 'email', 'max:150', 'unique:usuarios,email,' . $usuario->id],
        ]);

        // Actualizar persona
        $usuario->persona->update([
            'nombre'   => $request->nombre,
            'apellido' => $request->apellido,
            'telefono' => $request->telefono,
        ]);

        // Actualizar usuario
        $usuario->update([
            'email' => $request->email,
        ]);

        return redirect()
            ->route('cliente.perfil')
            ->with('success', 'Perfil actualizado correctamente.');
    }

    // ── Helpers ──────────────────────────────────────────────

    private function getSesionesPorMes($cliente): array
    {
        $meses   = [];
        $totales = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes = Carbon::now()->subMonths($i);

            $meses[]   = $mes->translatedFormat('M');
            $totales[] = $cliente->reservas()
                ->where('estado', 'COMPLETADA')
                ->whereYear('fecha_inicio', $mes->year)
                ->whereMonth('fecha_inicio', $mes->month)
                ->count();
        }

        return ['meses' => $meses, 'totales' => $totales];
    }
}
