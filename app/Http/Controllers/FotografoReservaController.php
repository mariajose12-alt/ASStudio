<?php

namespace App\Http\Controllers;

use App\DTOs\AccionReservaDTO;
use App\Models\Reserva;
use App\Services\FotografoReservaService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FotografoReservaController extends Controller
{
    public function __construct(
        private FotografoReservaService $reservaService
    ) {}

    public function index()
    {
        $fotografo = Auth::user()->empleado->fotografo;

        $reservas = $this->reservaService->todasLasReservas($fotografo->id);

        return view('fotografo.reservas.index', compact('reservas'));
    }

    public function show(Reserva $reserva)
    {
        $this->authorize('verComoFotografo', $reserva);

        $reserva->load(['cliente.usuario.persona', 'paquete']);

        return view('fotografo.reservas.show', compact('reserva'));
    }

    public function procesarAccion(Request $request, Reserva $reserva)
    {
        $this->authorize('verComoFotografo', $reserva);

        $request->validate([
            'accion'      => 'required|in:APROBADA,RECHAZADA,MODIFICACION_PROPUESTA,CERRAR_SESION',
            'motivo'      => 'required_if:accion,RECHAZADA,MODIFICACION_PROPUESTA|nullable|string|max:500',
            'duracion_horas' => 'nullable|numeric|min:0.5|max:12',
        ]);

        $fotografo = Auth::user()->empleado->fotografo;

        try {
            $dto = AccionReservaDTO::fromRequest($request);
            $this->reservaService->procesarAccion($reserva, $dto, $fotografo);

            $mensaje = match ($request->accion) {
                'APROBADA'   => 'Reserva aprobada. El cliente fue notificado.',
                'RECHAZADA'  => 'Reserva rechazada. El cliente fue notificado.',
                'MODIFICACION_PROPUESTA' => 'Propuesta enviada al cliente.',
                'CERRAR_SESION'  => 'Sesión cerrada.',
            };

            return redirect()
                ->route('fotografo.reservas.index')
                ->with('success', $mensaje);

        } catch (\Exception $e) {
            return back()->with('error', 'Ocurrió un error al procesar la reserva. Intenta de nuevo.');
        }
    }
}
