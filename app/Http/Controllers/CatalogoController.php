<?php

namespace App\Http\Controllers;

use App\Models\Catalogo;
use App\Models\PaqueteFotografico;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CatalogoController extends Controller
{
    public function index()
    {
        $catalogos = Catalogo::with('creador.persona')->paginate(10);
        return view('admin.catalogos.index', compact('catalogos'));
    }

    public function create()
    {
        $paquetes = PaqueteFotografico::where('activo', true)->get();
        return view('admin.catalogos.create', compact('paquetes'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string',
            'activo'                => 'boolean',
            'fecha_inicio_vigencia' => 'nullable|date',
            'fecha_fin_vigencia'    => 'nullable|date|after_or_equal:fecha_inicio_vigencia',
            'paquetes'              => 'nullable|array',
            'paquetes.*'            => 'exists:paquetes_fotograficos,id',
        ]);

        $data['activo']     = $request->boolean('activo');
        $data['creador_id'] = Auth::id();

        $catalogo = Catalogo::create($data);

        // Sincronizar paquetes en la tabla pivote
        $catalogo->paquetes()->sync($request->input('paquetes', []));

        return redirect()
            ->route('admin.catalogos.index')
            ->with('success', 'Catálogo creado correctamente.');
    }

    public function show(Catalogo $catalogo)
    {
        $catalogo->load('creador.persona', 'paquetes');
        return view('admin.catalogos.show', compact('catalogo'));
    }

    public function edit(Catalogo $catalogo)
    {
        $paquetes = PaqueteFotografico::where('activo', true)->get();
        $catalogo->load('paquetes');
        return view('admin.catalogos.edit', compact('catalogo', 'paquetes'));
    }

    public function update(Request $request, Catalogo $catalogo)
    {
        $data = $request->validate([
            'nombre'                => 'required|string|max:150',
            'descripcion'           => 'nullable|string',
            'activo'                => 'boolean',
            'fecha_inicio_vigencia' => 'nullable|date',
            'fecha_fin_vigencia'    => 'nullable|date|after_or_equal:fecha_inicio_vigencia',
            'paquetes'              => 'nullable|array',
            'paquetes.*'            => 'exists:paquetes_fotograficos,id',
        ]);

        $data['activo'] = $request->boolean('activo');

        $catalogo->update($data);

        // Sincronizar paquetes en la tabla pivote
        $catalogo->paquetes()->sync($request->input('paquetes', []));

        return redirect()
            ->route('admin.catalogos.index')
            ->with('success', 'Catálogo actualizado correctamente.');
    }

    public function destroy(Catalogo $catalogo)
    {
        $catalogo->delete();

        return redirect()
            ->route('admin.catalogos.index')
            ->with('success', 'Catálogo eliminado correctamente.');
    }
}
