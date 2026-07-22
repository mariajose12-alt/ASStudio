<?php

namespace App\Http\Controllers;
use App\Models\DetalleNomina;
use App\Models\ParticipacionSesion;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class FotografoNominaController extends Controller
{
    public function index()
    {
        $fotografo = Auth::user()->empleado->fotografo;

        $detalles = DetalleNomina::where('fotografo_id', $fotografo->id)
            ->with('nomina')
            ->latest('id')
            ->get();

        return view('fotografo.nomina.index', compact('detalles'));
    }

    public function show(DetalleNomina $detalle)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        abort_if($detalle->fotografo_id !== $fotografo->id,
            403, 'No tienes permiso para ver este detalle.');

        $detalle->load('nomina');

        $fechaInicio   = $detalle->nomina->fecha_inicio;
        $fechaFin      = $detalle->nomina->fecha_fin;

        $participaciones = ParticipacionSesion::where('fotografo_id', $fotografo->id)
            ->whereHas('sesion', function ($q) use ($fechaInicio) {
                $q->where('estado', 'FINALIZADA')
                    ->whereYear('fecha_finalizacion', $fechaInicio->year)
                    ->whereMonth('fecha_finalizacion', $fechaInicio->month);
            })
            ->where('estado_participacion', true)
            ->with('sesion.reserva')
            ->get();

        return view('fotografo.nomina.show', compact('detalle', 'participaciones'));
    }

    public function confirmar(DetalleNomina $detalle)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        abort_if($detalle->fotografo_id !== $fotografo->id,
            403, 'No tienes permiso para confirmar este detalle.');
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');

        if($detalle->confirmado_por_fotografo){
            return back()->with('error', 'Este detalle ya ha sido confirmado.');
        }

        $detalle->update([
            'estado_confirmacion'      => 'CONFIRMADO',
            'confirmado_at'            => now(),
        ]);

        return back()->with('success', 'Detalle confirmado correctamente.');
    }

    public function reportarAjuste( Request $request, DetalleNomina $detalle)
    {
        $fotografo = Auth::user()->empleado->fotografo;

        abort_if($detalle->fotografo_id !== $fotografo->id,
            403, 'No tienes permiso para reportar este detalle.');
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');

        if(!$detalle->estaPendiente()){
            return back()->with('error', 'Este detalle ya ha sido reportado.');
        }

        $request->validate([
            'observacion_fotografo' => 'required|string|min:10|max:255'
        ],
            [
                'observacion_fotografo.required' => 'Debes explicar el ajuste necesitas.',
                'observacion_fotografo.min'      => 'Por favor brinda más detalle sobre el ajuste (mínimo 10 caracteres).',
            ]);

        $detalle->update([
            'estado_confirmacion' => 'EN_DISPUTA',
            'observacion_fotografo' => $request->observacion_fotografo,
        ]);

        return back()->with('success', 'Tu observación fue registrada. Detalle reportado correctamente.');

    }
}
