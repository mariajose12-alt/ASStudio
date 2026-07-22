<?php
namespace App\Http\Controllers;

use App\Models\BloqueoEstudio;
use Illuminate\Http\Request;

class BloqueoEstudioController extends Controller
{
    public function index()
    {
        return view('admin.estudio');
    }

    public function eventos()
    {
        return BloqueoEstudio::all()->map(fn($b) => [
            'id'    => $b->id,
            'title' => $b->motivo ?? 'Ocupado',
            'start' => $b->inicio->toIso8601String(),
            'end'   => $b->fin->toIso8601String(),
            'color' => '#e87722',
        ]);
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
