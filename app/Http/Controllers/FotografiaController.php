<?php

namespace App\Http\Controllers;

use App\Jobs\GenerarThumbnailFoto;
use App\Notifications\FotosEntregadasCliente;
use App\Notifications\GaleriaDisponibleCliente;
use App\Notifications\SeleccionConfirmadaFotografo;
use App\Models\Fotografia;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Laravel\Facades\Image;
use Illuminate\Support\Facades\Storage;

class FotografiaController extends Controller
{
    public function create(int $id)
    {
        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::with([
            'reserva.cliente.usuario.persona',
            'reserva.paquete',
            'fotografias.subidaPor.empleado.usuario.persona',
        ])
            ->whereIn('estado', ['EN_PROCESO', 'EN_EDICION', 'GALERIA_DISPONIBLE'])
            ->findOrFail($id);

        $this->authorize('gestionar', $sesion);

        $esPrincipal = $sesion->esPrincipalDe($fotografo);

        // Los RAW nunca requieren aprobación, así que esto en la práctica solo excluye editadas pendientes
        $aprobadas = $sesion->fotografias->where('aprobada', true);

        $totalPendientes = $aprobadas->where('estado', 'PENDIENTE_EDICION')->count();
        $totalEditadas   = $aprobadas->where('estado', 'EDITADA')->count();
        $todasEditadas   = $totalPendientes > 0 && $totalEditadas >= $totalPendientes;

        $pendientesEdicion = $aprobadas
            ->where('seleccionada', true)
            ->where('estado', 'PENDIENTE_EDICION')
            ->map(function ($foto) use ($todasEditadas) {
                $foto->ya_editada = $todasEditadas;
                return $foto;
            });

        // Solo el principal ve y gestiona las editadas pendientes de aprobación
        $pendientesAprobacion = $esPrincipal
            ? $sesion->fotografias->where('aprobada', false)->values()
            : collect();

        return view('fotografo.fotografias', compact(
            'sesion',
            'pendientesEdicion',
            'pendientesAprobacion',
            'esPrincipal',
            'totalPendientes',
            'totalEditadas'
        ));
    }

    public function store(Request $request, int $sesionId)
    {
        $request->validate([
            'fotos'   => 'required|array|min:1',
            'fotos.*' => 'required|file|mimes:jpg,jpeg,png,webp,raw,cr2,nef,arw|max:102400',
            'estado'  => 'required|in:ORIGINAL,EDITADA',
        ]);

        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::with('reserva.cliente.usuario')->findOrFail($sesionId);

        $this->authorize('gestionar', $sesion);

        $esPrincipal = $sesion->esPrincipalDe($fotografo);

        $estado  = $request->estado;
        $carpeta = $estado === 'ORIGINAL' ? 'raw' : 'editadas';
        $guardadas = [];

        foreach ($request->file('fotos') as $archivo) {
            $nombreOriginal = $archivo->getClientOriginalName();

            $path = Storage::disk('r2')->putFile(
                "sesiones/{$sesionId}/{$carpeta}",
                $archivo
            );

            $foto = Fotografia::create([
                'sesion_id'                => $sesionId,
                'subido_por_fotografo_id'  => $fotografo->id,
                'url'                      => $path,
                'nombre_original'          => $nombreOriginal,
                'estado'                   => $estado,
                // Los RAW nunca requieren aprobación; las editadas sí, si las sube un asistente
                'aprobada'                 => $estado === 'ORIGINAL' ? true : $esPrincipal,
            ]);

            $extension = strtolower($archivo->getClientOriginalExtension());
            $formatosConThumb = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($extension, $formatosConThumb)) {
                GenerarThumbnailFoto::dispatch($foto->id);
            }

            $guardadas[] = $foto;
        }

        $huboPendientes = ! $esPrincipal && $estado === 'EDITADA';

        return response()->json([
            'message' => count($guardadas) . ' foto(s) subida(s) correctamente'
                . ($huboPendientes ? ' — pendientes de aprobación del fotógrafo principal.' : ''),
            'fotos' => collect($guardadas)->map(fn($f) => [
                'id'           => $f->id,
                'nombre'       => $f->nombre_original ?? basename($f->url),
                'estado'       => $f->estado,
                'estado_lower' => strtolower($f->estado),
                'aprobada'     => $f->aprobada,
            ])->values(),
        ], 201);
    }

    /**
     * El fotógrafo principal aprueba una foto EDITADA subida por un asistente.
     */
    public function aprobar(int $id)
    {
        $fotografo = auth()->user()->empleado->fotografo;

        $foto = Fotografia::with('sesion.reserva')->findOrFail($id);

        $this->authorize('esPrincipal', $foto);

        $foto->update(['aprobada' => true]);

        return response()->json(['message' => 'Foto editada aprobada correctamente.']);
    }

    /**
     * El fotógrafo principal rechaza (y elimina) una foto EDITADA subida por un asistente.
     */
    public function rechazar(int $id)
    {
        $fotografo = auth()->user()->empleado->fotografo;

        $foto = Fotografia::with('sesion')->findOrFail($id);

        $this->authorize('esPrincipal', $foto);

        Storage::disk('r2')->delete($foto->url);
        $foto->delete();

        return response()->json(['message' => 'Foto rechazada y eliminada.']);
    }

    /**
     * El fotógrafo PRINCIPAL confirma que ya subió todas las fotos originales
     * y dispara el aviso al cliente de que la galería está lista para elegir.
     * A diferencia de antes, esto ya no ocurre solo con subir la primera foto —
     * así se evita que el cliente entre a elegir favoritas cuando todavía
     * faltan fotos por subir (de él o de algún ayudante) y se quede sin poder
     * completar el límite de su paquete.
     */
    public function marcarGaleriaDisponible(int $sesionId)
    {
        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::with('reserva.cliente.usuario', 'reserva.paquete', 'fotografias')
            ->whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->where('estado', 'EN_PROCESO')
            ->findOrFail($sesionId);

        $totalOriginales = $sesion->fotografias->where('estado', 'ORIGINAL')->count();
        $minimoRequerido = $sesion->reserva->paquete->cantidad_fotos_incluidas ?? null;

        if ($totalOriginales === 0) {
            return response()->json([
                'message' => 'Sube al menos una foto antes de avisarle al cliente.',
            ], 422);
        }

        if ($minimoRequerido && $totalOriginales < $minimoRequerido) {
            $faltan = $minimoRequerido - $totalOriginales;
            return response()->json([
                'message' => "Aún faltan {$faltan} foto(s) para llegar al mínimo del paquete "
                    . "({$totalOriginales}/{$minimoRequerido}).",
            ], 422);
        }

        $sesion->update(['estado' => 'GALERIA_DISPONIBLE']);

        $usuario = $sesion->reserva->cliente->usuario ?? null;
        if ($usuario) {
            $usuario->notify(new GaleriaDisponibleCliente($sesion));
        }

        return response()->json([
            'message' => 'Galería enviada. El cliente ya puede elegir sus fotos favoritas.',
        ]);
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

        $usuario = $sesion->reserva->cliente->usuario ?? null;
        if ($usuario) {
            $usuario->notify(new FotosEntregadasCliente($sesion));
        }

        return back()->with('success', 'Sesión marcada como entregada. El cliente ha sido notificado.');
    }

    public function download(int $id)
    {
        $foto = Fotografia::with('sesion')->findOrFail($id);

        $this->authorize('gestionar', $foto);

        $url = Storage::disk('r2')->temporaryUrl($foto->url, now()->addMinutes(60));
        return response()->json(['url' => $url]);
    }
    public function destroy(int $id)
    {
        $foto = Fotografia::with('sesion')->findOrFail($id);

        $this->authorize('esPrincipal', $foto);

        Storage::disk('r2')->delete($foto->url);
        $foto->delete();
        return response()->json(['message' => 'Foto eliminada correctamente']);
    }
}
