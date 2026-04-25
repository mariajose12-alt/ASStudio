<?php

namespace App\Http\Controllers;

use App\Models\PaqueteFotografico;
use Illuminate\Http\Request;

class PaqueteController extends Controller
{
    public function index()
    {
        $paquetes = PaqueteFotografico::paginate(10);
        return view('admin.paquetes.index', compact('paquetes'));
    }

    public function create()
    {
        return view('admin.paquetes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                   => 'required|string|max:150',
            'descripcion'              => 'nullable|string',
            'precio_base'              => 'required|numeric|min:0',
            'cantidad_fotos_incluidas' => 'required|integer|min:1',
            'activo'                   => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo');

        PaqueteFotografico::create($data);

        return redirect()
            ->route('admin.paquetes.index')
            ->with('success', 'Paquete creado correctamente.');
    }

    public function show(PaqueteFotografico $paquete)
    {
        return view('admin.paquetes.show', compact('paquete'));
    }

    public function edit(PaqueteFotografico $paquete)
    {
        return view('admin.paquetes.edit', compact('paquete'));
    }

    public function update(Request $request, PaqueteFotografico $paquete)
    {
        $data = $request->validate([
            'nombre'                   => 'required|string|max:150',
            'descripcion'              => 'nullable|string',
            'precio_base'              => 'required|numeric|min:0',
            'cantidad_fotos_incluidas' => 'required|integer|min:1',
            'activo'                   => 'boolean',
        ]);

        $data['activo'] = $request->boolean('activo');

        $paquete->update($data);

        return redirect()
            ->route('admin.paquetes.index')
            ->with('success', 'Paquete actualizado correctamente.');
    }

    public function destroy(PaqueteFotografico $paquete)
    {
        $paquete->delete();

        return redirect()
            ->route('admin.paquetes.index')
            ->with('success', 'Paquete eliminado correctamente.');
    }
}
