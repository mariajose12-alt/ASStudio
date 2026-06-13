<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\FotosEntregadasCliente;
use App\Mail\GaleriaDisponibleCliente;
use App\Mail\SeleccionConfirmadaFotografo;
use App\Models\Fotografia;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;

class FotografiaController extends Controller
{
    public function create(int $id)
    {
        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::with([
            'reserva.cliente.usuario.persona',
            'reserva.paquete',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->whereIn('estado', ['EN_PROCESO', 'EN_EDICION', 'GALERIA_DISPONIBLE'])
            ->findOrFail($id);

        $totalPendientes = $sesion->fotografias->where('estado', 'PENDIENTE_EDICION')->count();
        $totalEditadas   = $sesion->fotografias->where('estado', 'EDITADA')->count();
        $todasEditadas   = $totalPendientes > 0 && $totalEditadas >= $totalPendientes;

        $pendientesEdicion = $sesion->fotografias
            ->where('seleccionada', true)
            ->where('estado', 'PENDIENTE_EDICION')
            ->map(function ($foto) use ($todasEditadas) {
                $foto->ya_editada = $todasEditadas;
                return $foto;
            });

        return view('fotografo.fotografias', compact('sesion', 'pendientesEdicion'));
    }

    public function store(Request $request, int $sesionId)
    {
        $request->validate([
            'fotos'   => 'required|array|min:1',
            'fotos.*' => 'required|file|mimes:jpg,jpeg,png,webp,raw,cr2,nef,arw|max:102400',
            'estado'  => 'required|in:ORIGINAL,EDITADA',
        ]);

        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::with('reserva.cliente.usuario')
            ->whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->findOrFail($sesionId);

        $estado  = $request->estado;
        $carpeta = $estado === 'ORIGINAL' ? 'raw' : 'editadas';
        $guardadas = [];

        foreach ($request->file('fotos') as $archivo) {
            $nombreOriginal = $archivo->getClientOriginalName();

            $path = Storage::disk('r2')->putFile(
                "sesiones/{$sesionId}/{$carpeta}",
                $archivo
            );

            $guardadas[] = Fotografia::create([
                'sesion_id'       => $sesionId,
                'url'             => $path,
                'nombre_original' => $nombreOriginal,
                'estado'          => $estado,
            ]);
        }

        // Avance de estado + notificación al cliente cuando se suben las originales
        if ($estado === 'ORIGINAL' && $sesion->estado === 'EN_PROCESO') {
            $sesion->update(['estado' => 'GALERIA_DISPONIBLE']);

            $clienteEmail = $sesion->reserva->cliente->usuario->email ?? null;
            if ($clienteEmail) {
                Mail::to($clienteEmail)->send(new GaleriaDisponibleCliente($sesion));
            }
        }

        return response()->json([
            'message' => count($guardadas) . ' foto(s) subida(s) correctamente',
            'fotos' => collect($guardadas)->map(fn($f) => [
                'id'           => $f->id,
                'nombre'       => $f->nombre_original ?? basename($f->url),
                'estado'       => $f->estado,
                'estado_lower' => strtolower($f->estado),
            ])->values(),
        ], 201);
    }

    /**
     * El fotógrafo declara que terminó de subir las fotos editadas.
     * Cambia el estado de la sesión a ENTREGADA y notifica al cliente.
     */
    public function marcarEntregada(int $sesionId)
    {
        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::with('reserva.cliente.usuario')
            ->whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->where('estado', 'EN_EDICION')
            ->findOrFail($sesionId);

        $sesion->update(['estado' => 'FINALIZADA']);

        $clienteEmail = $sesion->reserva->cliente->usuario->email ?? null;
        if ($clienteEmail) {
            Mail::to($clienteEmail)->send(new FotosEntregadasCliente($sesion));
        }

        return response()->json([
            'message' => 'Sesión marcada como entregada. El cliente ha sido notificado.',
        ]);
    }

    /**
     * El cliente confirma su selección final de fotos.
     * Se ejecuta UNA sola vez — después no hay vuelta atrás.
     */
    public function confirmarSeleccion(Request $request, int $sesionId)
    {
        $request->validate([
            'fotos_seleccionadas' => 'required|json',
        ]);

        $ids = json_decode($request->fotos_seleccionadas, true);

        if (! is_array($ids) || empty($ids)) {
            return back()->withErrors(['fotos_seleccionadas' => 'Debes seleccionar al menos una foto.']);
        }

        $sesion = Sesion::with('reserva.fotografo.empleado.usuario')
            ->whereHas('reserva.cliente', function ($q) {
                $q->where('usuario_id', auth()->id());
            })->findOrFail($sesionId);

        if ($sesion->estado === 'EN_EDICION') {
            return back()->withErrors(['fotos_seleccionadas' => 'Ya confirmaste tu selección para esta sesión.']);
        }

        Fotografia::where('sesion_id', $sesionId)
            ->whereIn('id', $ids)
            ->where('estado', 'ORIGINAL')
            ->update([
                'seleccionada' => true,
                'estado'       => 'PENDIENTE_EDICION',
            ]);

        $sesion->update(['estado' => 'EN_EDICION']);

        // Notificar al fotógrafo
        $fotografoEmail = $sesion->reserva->fotografo->empleado->usuario->email ?? null;
        if ($fotografoEmail) {
            Mail::to($fotografoEmail)->send(new SeleccionConfirmadaFotografo($sesion));
        }

        return redirect()->route('cliente.galeria')
            ->with('success', 'Selección confirmada. El fotógrafo editará tus fotos en los próximos días.');
    }

    public function download(int $id)
    {
        $fotografia = Fotografia::findOrFail($id);
        $url = Storage::disk('r2')->temporaryUrl($fotografia->url, now()->addMinutes(60));
        return response()->json(['url' => $url]);
    }

    public function destroy(int $id)
    {
        $fotografia = Fotografia::findOrFail($id);
        Storage::disk('r2')->delete($fotografia->url);
        $fotografia->delete();
        return response()->json(['message' => 'Foto eliminada correctamente']);
    }
}
