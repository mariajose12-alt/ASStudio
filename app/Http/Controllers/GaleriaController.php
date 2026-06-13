<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Fotografia;
use App\Services\GaleriaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GaleriaController extends Controller
{
    public function __construct(private readonly GaleriaService $galeria) {}

    // Índice — sesiones con galería disponible o finalizadas
    public function index()
    {
        $sesiones = $this->galeria->sesionesDelCliente($this->clienteId());

        return view('cliente.galeria.index', compact('sesiones'));
    }

    // Paso 1 — selección de RAW (solo si estado es GALERIA_DISPONIBLE)
    public function show(int $id)
    {
        $sesion = $this->galeria->sesionParaSeleccion($id, $this->clienteId());

        if (in_array($sesion->estado, ['EN_EDICION', 'FINALIZADA'])) {
            return redirect()->route('cliente.galeria.final', $id);
        }

        $fotos  = $this->galeria->fotosOriginalesConUrl($sesion);
        $limite = $sesion->reserva->paquete->cantidad_fotos_incluidas;

        return view('cliente.galeria.seleccion', compact('sesion', 'fotos', 'limite'));
    }

    // Confirmar selección → estado pasa a EN_EDICION
    public function confirmar(Request $request, int $id)
    {
        $sesion = $this->galeria->sesionParaSeleccion($id, $this->clienteId());

        abort_if($sesion->estado !== 'GALERIA_DISPONIBLE', 403);

        $seleccionadas = json_decode($request->input('fotos_seleccionadas'), true) ?? [];

        $this->galeria->confirmarSeleccion($sesion, $seleccionadas);

        return redirect()
            ->route('cliente.galeria.final', $id)
            ->with('success', '¡Selección guardada! El fotógrafo editará tus fotos pronto.');
    }

    // Paso 2 — galería final con EDITADAS
    public function final(int $id)
    {
        $sesion = $this->galeria->sesionParaFinal($id, $this->clienteId());
        $fotos  = $this->galeria->fotosFinalesConUrl($sesion);

        return view('cliente.galeria.final', compact('sesion', 'fotos'));
    }

    // Confirmar recepción → estado pasa a FINALIZADA
    public function confirmarRecepcion(int $id)
    {
        $sesion = $this->galeria->sesionParaSeleccion($id, $this->clienteId());

        abort_if($sesion->estado !== 'EN_EDICION', 403);

        $this->galeria->confirmarRecepcion($sesion);

        return redirect()
            ->route('cliente.galeria.final', $id)
            ->with('success', '¡Gracias! Tus fotos han sido confirmadas.');
    }

    // Descarga individual
    public function descargar(int $id)
    {
        $foto = Fotografia::whereHas(
            'sesion.reserva',
            fn($q) => $q->where('cliente_id', $this->clienteId())
        )->findOrFail($id);

        return redirect($this->galeria->urlTemporalFoto($foto));
    }

    // Encolar generación del ZIP → el cliente recibe notificación cuando esté listo
    public function descargarZip(int $id, string $tipo)
    {
        $sesion = $this->galeria->sesionParaFinal($id, $this->clienteId());

        try {
            $this->galeria->despacharZip($sesion, $tipo);
        } catch (\InvalidArgumentException $e) {
            dd('InvalidArgumentException', $e->getMessage(), $e->getFile(), $e->getLine());
        } catch (\RuntimeException $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('info', 'Estamos preparando tu ZIP. Te notificaremos cuando esté listo para descargar.');
    }

    // Helpers

    private function clienteId(): int
    {
        return Auth::user()->cliente->id;
    }
}
