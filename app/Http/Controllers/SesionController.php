<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Sesion;
use Illuminate\Support\Facades\Auth;

class SesionController extends Controller
{
    public function index()
    {
        $fotografo = Auth::user()->empleado->fotografo;

        $sesiones = Sesion::with([
            'reserva.cliente.usuario.persona',
            'reserva.paquete',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->whereIn('estado', ['EN_PROCESO', 'EN_EDICION'])
            ->orderByDesc('fecha_inicio')
            ->get();

        $enProceso = $sesiones->where('estado', 'EN_PROCESO')->values();
        $enEdicion = $sesiones->where('estado', 'EN_EDICION')->values();

        return view('fotografo.sesiones', compact('enProceso', 'enEdicion'));
    }

    // Botón provisional para pasar de CONFIRMADA a EN_PROCESO
    public function iniciar(int $id)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        $sesion = Sesion::whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->where('estado', 'CONFIRMADA')
            ->findOrFail($id);

        $sesion->update(['estado' => 'EN_PROCESO']);

        return back()->with('success', 'Sesión marcada como En Proceso.');
    }
}
