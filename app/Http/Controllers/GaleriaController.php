<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fotografia;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use ZipArchive;
use Illuminate\Support\Facades\Http;
class GaleriaController extends Controller
{
    // Índice — sesiones con galería disponible o finalizadas
    public function index()
    {
        $cliente = Auth::user()->cliente;

        $sesiones = Sesion::with([
            'reserva.paquete',
            'reserva.fotografo.empleado.usuario.persona',
            'reserva.cliente.usuario.persona',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->whereIn('estado', ['GALERIA_DISPONIBLE', 'EN_EDICION', 'FINALIZADA'])
            ->orderByDesc('fecha_inicio')
            ->get();

        return view('cliente.galeria.index', compact('sesiones'));
    }

    // Paso 1 — selección de RAW (solo si estado es GALERIA_DISPONIBLE)
    public function show(int $id)
    {
        $cliente = Auth::user()->cliente;

        $sesion = Sesion::with([
            'reserva.paquete',
            'reserva.cliente.usuario.persona',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->findOrFail($id);

        // Si ya confirmó selección, ir a galería final
        if (in_array($sesion->estado, ['EN_EDICION', 'FINALIZADA'])) {
            return redirect()->route('cliente.galeria.final', $id);
        }

        $fotos  = $sesion->fotografias->where('estado', 'ORIGINAL')->values();
        $limite = $sesion->reserva->paquete->cantidad_fotos_incluidas;

        return view('cliente.galeria.seleccion', compact('sesion', 'fotos', 'limite'));
    }

    // Confirmar selección → estado pasa a EN_EDICION
    public function confirmar(Request $request, int $id)
    {
        $cliente = Auth::user()->cliente;

        $sesion = Sesion::whereHas('reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->where('estado', 'GALERIA_DISPONIBLE')
            ->findOrFail($id);

        $limite        = $sesion->reserva->paquete->cantidad_fotos_incluidas;
        $seleccionadas = json_decode($request->input('fotos_seleccionadas'), true) ?? [];
        $seleccionadas = array_slice($seleccionadas, 0, $limite);

        Fotografia::where('sesion_id', $id)->where('estado', 'ORIGINAL')->update(['seleccionada' => false]);
        Fotografia::whereIn('id', $seleccionadas)->where('sesion_id', $id)->update(['seleccionada' => true]);

        $sesion->update(['estado' => 'EN_EDICION']);

        return redirect()->route('cliente.galeria')->with('success', '¡Selección guardada! El fotógrafo editará tus fotos pronto.');
    }

    // Paso 2 — galería final con EDITADAS
    public function final(int $id)
    {
        $cliente = Auth::user()->cliente;

        $sesion = Sesion::with([
            'reserva.paquete',
            'reserva.cliente.usuario.persona',
            'fotografias',
        ])
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->findOrFail($id);

        $fotos = $sesion->fotografias->whereIn('estado', ['ORIGINAL', 'EDITADA'])->values();

        return view('cliente.galeria.final', compact('sesion', 'fotos'));
    }

    // Confirmar recepción → estado pasa a FINALIZADA
    public function confirmarRecepcion(int $id)
    {
        $cliente = Auth::user()->cliente;

        $sesion = Sesion::whereHas('reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->where('estado', 'EN_EDICION')
            ->findOrFail($id);

        $sesion->update(['estado' => 'FINALIZADA']);

        return redirect()->route('cliente.galeria.final', $id)->with('success', '¡Gracias! Tus fotos han sido confirmadas.');
    }

    // Descarga individual
    public function descargar(int $id)
    {
        $cliente = Auth::user()->cliente;

        $foto = Fotografia::whereHas('sesion.reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->findOrFail($id);

        $url = Storage::disk('r2')->temporaryUrl($foto->url, now()->addMinutes(5));

        return redirect($url);
    }

    // Generar un zip con todas las fotos
    public function descargarZip(int $id, string $tipo)
    {
        $cliente = Auth::user()->cliente;

        $sesion = Sesion::with('fotografias')
            ->whereHas('reserva', fn($q) => $q->where('cliente_id', $cliente->id))
            ->findOrFail($id);

        $estado = match($tipo) {
            'originales' => 'ORIGINAL',
            'editadas'   => 'EDITADA',
            default      => abort(404),
        };

        $fotos = $sesion->fotografias->where('estado', $estado)->values();

        if ($fotos->isEmpty()) {
            return back()->with('error', 'No hay fotos disponibles para descargar.');
        }

        $zipNombre = "fotos_{$tipo}_{$id}.zip";
        $zipPath   = storage_path("app/tmp/{$zipNombre}");

        // Asegurar que exista el directorio temporal
        if (!file_exists(storage_path('app/tmp'))) {
            mkdir(storage_path('app/tmp'), 0755, true);
        }

        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            return back()->with('error', 'No se pudo crear el archivo ZIP.');
        }

        foreach ($fotos as $foto) {
            // URL firmada de corta duración solo para descarga interna
            $url = Storage::disk('r2')->temporaryUrl($foto->url, now()->addMinutes(10));
            $contenido = Http::timeout(30)->get($url)->body();

            if ($contenido) {
                $nombreArchivo = basename($foto->url);
                $zip->addFromString($nombreArchivo, $contenido);
            }
        }

        $zip->close();

        return response()->download($zipPath, $zipNombre, [
            'Content-Type'              => 'application/zip',
            'Content-Disposition'       => 'attachment; filename="' . $zipNombre . '"',
            'Access-Control-Allow-Origin' => '*',
        ])->deleteFileAfterSend(true);
    }
}
