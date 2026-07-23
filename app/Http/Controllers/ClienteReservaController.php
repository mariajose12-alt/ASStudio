<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Reserva;
use App\Services\FotografoReservaService;
use Illuminate\Http\Request;

class ClienteReservaController extends Controller
{
    public function __construct(
        private FotografoReservaService $reservaService
    ) {}

    public function index()
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->first();

        $reservas = $cliente
            ? $cliente->reservas()->with(['paquete', 'catalogo'])->latest()->get()
            : collect();

        return view('cliente.reservas.index', compact('reservas'));
    }

    public function show(Reserva $reserva)
    {
        $this->autorizarPropietario($reserva);

        $reserva->load(['paquete', 'catalogo']);

        return view('cliente.reservas.show', compact('reserva'));
    }

    public function responderSugerencia(Request $request, Reserva $reserva)
    {
        $this->autorizarPropietario($reserva);

        $request->validate([
            'accion'      => 'required|in:ACEPTAR,EDITAR,CANCELAR',
            'descripcion' => 'required_if:accion,EDITAR|nullable|string|max:1000',
        ]);

        try {
            match ($request->accion) {
                'ACEPTAR'  => $this->reservaService->aprobarPorAceptacionCliente($reserva),
                'CANCELAR' => $reserva->update(['estado' => 'CANCELADA']),
                'EDITAR'   => $reserva->update([
                    'estado'                    => 'PENDIENTE',
                    'descripcion'               => $request->descripcion,
                    'motivo_rechazo'            => null,
                    'duracion_horas_propuesta'  => null,
                ]),
            };
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('cliente.reservas.index')
            ->with('success', match($request->accion) {
                'ACEPTAR'  => 'Sugerencia aceptada. Tu sesión quedó confirmada.',
                'CANCELAR' => 'Reserva cancelada.',
                'EDITAR'   => 'Reserva reenviada para revisión.',
            });
    }

    /**
     * Verifica que la reserva pertenezca al cliente autenticado.
     * Aborta con 403 si no hay cliente o si la reserva es de otro cliente.
     */
    private function autorizarPropietario(Reserva $reserva): void
    {
        $cliente = Cliente::where('usuario_id', auth()->id())->firstOrFail();

        abort_if($reserva->cliente_id !== $cliente->id, 403);
    }
}
