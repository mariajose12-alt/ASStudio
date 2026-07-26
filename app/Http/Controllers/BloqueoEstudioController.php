<?php
namespace App\Http\Controllers;

use App\Models\BloqueoEstudio;
use Illuminate\Http\Request;
use App\Models\SolicitudEstudio;
use Carbon\Carbon;


class BloqueoEstudioController extends Controller
{
    public function index()
    {
        return view('admin.estudio.solicitudes.index');
    }

    public function eventos()
    {
        $bloqueos = BloqueoEstudio::all()->map(fn($b) => [
            'id'     => 'bloqueo-' . $b->id,
            'tipo'   => 'bloqueo',
            'title'  => $b->motivo ?? 'Ocupado',
            'start'  => $b->inicio->format('Y-m-d\TH:i:s'),
            'end'    => $b->fin->format('Y-m-d\TH:i:s'),
            'color'  => '#e87722',
        ]);

        $solicitudes = SolicitudEstudio::whereIn('estado', ['pendiente', 'aprobada'])
            ->get()
            ->map(fn($s) => [
                'id'    => 'solicitud-' . $s->id,
                'tipo'  => 'solicitud',
                'title' => ($s->estado === 'aprobada' ? 'Reservado: ' : 'Pendiente: ') . "{$s->nombre} {$s->apellido}",
                'start' => Carbon::parse($s->fecha->toDateString() . ' ' . $s->hora_inicio->format('H:i:s'))->format('Y-m-d\TH:i:s'),
                'end'   => Carbon::parse($s->fecha->toDateString() . ' ' . $s->hora_fin->format('H:i:s'))->format('Y-m-d\TH:i:s'),
                'color' => $s->estado === 'aprobada' ? '#5b7091' : '#c9a34e',
            ]);

        return $bloqueos->concat($solicitudes)->values();
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'inicio' => 'required|date',
            'fin'    => 'required|date|after:inicio',
            'motivo' => 'nullable|string|max:255',
        ], [
            'fin.after' => 'La hora de fin debe ser posterior al inicio.',
        ]);

        if (BloqueoEstudio::solapaCon($validated['inicio'], $validated['fin'])->exists()) {
            return back()->withErrors(['inicio' => 'Ya existe un bloqueo en ese horario.']);
        }

        BloqueoEstudio::create([
            ...$validated,
            'creado_por_id' => auth()->id(),
        ]);

        return back()->with('success', 'Estudio marcado como ocupado.');
    }

    public function destroy(BloqueoEstudio $bloqueo)
    {
        $bloqueo->delete();
        return back()->with('success', 'Bloqueo eliminado.');
    }
}
