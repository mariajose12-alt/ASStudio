<?php

namespace App\Http\Controllers;

use App\Models\Nomina;
use App\Models\Reserva;
use App\Services\AdminService;
use Carbon\Carbon;
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
        // Meses para el dropdown (1 - 12 con su nombre)
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio',
            7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        // Años disponibles -> desde que existe el primer registro de nómina hasta la fecha actual
        $anioActual = now()->year;
        $primerAnio = Nomina::min('fecha_inicio')
            ? Carbon::parse(Nomina::min('fecha_inicio'))->year
            : $anioActual;

        $anios = range($anioActual, min($primerAnio, $anioActual));

        return view('admin.nomina', compact('meses', 'anios'));
    }
}
