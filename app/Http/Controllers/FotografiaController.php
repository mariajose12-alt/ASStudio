<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Mail\GaleriaDisponibleCliente;
use App\Models\Fotografia;
use App\Models\Sesion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Mail;

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
            ->whereIn('estado', ['EN_PROCESO', 'EN_EDICION'])
            ->findOrFail($id);

        return view('fotografo.fotografias', compact('sesion'));
    }

    public function store(Request $request, int $sesionId)
    {
        $request->validate([
            'fotos'   => 'required|array|min:1',
            'fotos.*' => 'required|file|mimes:jpg,jpeg,png,webp,raw,cr2,nef,arw|max:102400',
            'estado'  => 'required|in:ORIGINAL,EDITADA',
        ]);

        $fotografo = auth()->user()->empleado->fotografo;

        $sesion = Sesion::whereHas('reserva', fn($q) => $q->where('fotografo_id', $fotografo->id))
            ->findOrFail($sesionId);

        $estado  = $request->estado;
        $carpeta = $estado === 'ORIGINAL' ? 'raw' : 'editadas';
        $guardadas = [];

        foreach ($request->file('fotos') as $archivo) {
            $path = Storage::disk('r2')->putFile(
                "sesiones/{$sesionId}/{$carpeta}",
                $archivo
            );

            $guardadas[] = Fotografia::create([
                'sesion_id' => $sesionId,
                'url'       => $path,
                'estado'    => $estado,
            ]);
        }


        // Cambio de estado y notificación según lo que se subió
        if ($estado === 'ORIGINAL' && $sesion->estado === 'EN_PROCESO') {
            // Primera vez que se suben originales: galería disponible para selección
            $sesion->update(['estado' => 'GALERIA_DISPONIBLE']);
            $emailCliente = $sesion->reserva->cliente->usuario->email;
            Mail::to($emailCliente)->send(new GaleriaDisponibleCliente($sesion, 'seleccion'));

        } elseif ($estado === 'EDITADA' && $sesion->estado === 'EN_EDICION') {
            // Fotógrafo sube editadas: galería final disponible, notificar al cliente
            $sesion->update(['estado' => 'GALERIA_DISPONIBLE']);
            $emailCliente = $sesion->reserva->cliente->usuario->email;
            Mail::to($emailCliente)->send(new GaleriaDisponibleCliente($sesion, 'final'));
        }

        return response()->json([
            'message' => count($guardadas) . ' foto(s) subida(s) correctamente',
            'fotos'   => $guardadas,
        ], 201);
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
