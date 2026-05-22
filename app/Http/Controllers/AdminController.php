<?php

namespace App\Http\Controllers;

use App\Models\Reserva;
use App\Services\AdminService;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function __construct(
        private AdminService $adminService
    ) {}

    public function dashboard()
    {
        return view('admin.dashboard', $this->adminService->getData());
    }

    public function reservasIndex()
    {
        $reservas = Reserva::with(['cliente.usuario', 'paquete'])
            ->latest()
            ->paginate(15);

        return view('admin.reservas.index', compact('reservas'));
    }

    public function reservasShow(Reserva $reserva)
    {
        $reserva->load(['cliente.usuario', 'paquete.catalogos']);
        return view('admin.reservas.show', compact('reserva'));
    }

    public function reservasCambiarEstado(Request $request, Reserva $reserva)
    {
        $request->validate([
            'estado'         => 'required|in:APROBADA,RECHAZADA,CANCELADA,PENDIENTE,PAGO_RECIBIDO,MODIFICACION_PROPUESTA',
            'motivo_rechazo' => 'nullable|string|required_if:estado,MODIFICACION_PROPUESTA',
        ]);

        $this->adminService->cambiarEstadoReserva(
            $reserva,
            $request->estado,
            $request->motivo_rechazo
        );

        return redirect()->route('admin.reservas.index')
            ->with('success', 'Estado actualizado correctamente.');
    }

    public function estudio()
    {
        return view('admin.estudio');
    }


    public function nomina()
    {
        return view('admin.nomina');
    }
}
