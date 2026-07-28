<?php

namespace App\Http\Controllers;

use App\Events\ComprobanteSubido;
use App\Models\Comprobante;
use App\Models\CuentaBanco;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class ClientePagoController extends Controller
{
    /**
     * Lista los pagos pendientes del cliente autenticado.
     */
    public function index(): View
    {
        $clienteId = auth()->user()->cliente?->id;

        $pagos = Pago::where('cliente_id', $clienteId)
            ->whereIn('estado', ['PENDIENTE', 'RECHAZADO'])
            ->with('reserva')
            ->orderBy('fecha_registro')
            ->get();

        return view('cliente.pagos.index', compact('pagos'));
    }

    /**
     * Muestra el formulario para subir el comprobante de un pago pendiente.
     */
    public function formulario(Pago $pago): View
    {
        $this->authorize('verComoCliente', $pago);
        $cuentas = CuentaBanco::activas()->get()->groupBy('banco');

        return view('cliente.pagos.comprobante', compact('pago', 'cuentas'));
    }

    /**
     * Guarda el comprobante subido, lo asocia al pago y dispara el evento
     * que arranca la validación automática por OCR.
     */
    public function guardar(Request $request, Pago $pago): RedirectResponse
    {
        $this->authorize('verComoCliente', $pago);

        if (! $pago->puedeSubirComprobante()) {
            return back()->with('error', 'Este pago ya no está disponible para subir un comprobante.');
        }

        // Si el cliente eligió pago completo y el pago era ANTICIPO
        if ($pago->tipo === 'ANTICIPO' && $request->plan_pago === 'completo') {
            $total = $pago->reserva->paquete->precio_base;

            $pago->update([
                'tipo'  => 'COMPLETO',
                'monto' => $total,
            ]);

            $pago->reserva->pagos()
                ->where('tipo', 'FINAL')
                ->where('estado', 'PENDIENTE')
                ->delete();
        }

        $request->validate([
            'comprobante' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:8192'], // 8MB
        ], [
            'comprobante.mimes' => 'El comprobante debe ser una imagen en formato JPG o PNG.',
            'comprobante.max'   => 'El comprobante no debe pesar más de 8MB.',
        ]);

        $archivo = $request->file('comprobante');

        // Extensión derivada del tipo MIME real ya validado arriba
        // (jpg/jpeg/png), no del nombre de archivo que manda el cliente.
        $extension = match ($archivo->getMimeType()) {
            'image/png' => 'png',
            default     => 'jpg',
        };

        $key = sprintf(
            'comprobantes/%d/%s.%s',
            $pago->id,
            uniqid('comp_'),
            $extension
        );

        Storage::disk('r2')->put($key, file_get_contents($archivo->getRealPath()));

        $comprobante = Comprobante::create([
            'archivo_key' => $key,
            'estado_ocr'  => 'PENDIENTE',
        ]);

        $pago->asociarComprobante($comprobante);

        $pago->marcarEnRevision();

        event(new ComprobanteSubido($comprobante));

        return redirect()
            ->route('cliente.reservas.show', $pago->reserva_id)
            ->with('success', 'Tu comprobante fue recibido y está en revisión. Te avisaremos por correo cuando se confirme.');
    }
}
