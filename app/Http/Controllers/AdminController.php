<?php

namespace App\Http\Controllers;

use App\Models\Fotografo;
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


    public function nomina(Request $request)
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

        // Sí se seleccionó el periodo, traer los fotografos con sesiones en ese periodo.
        $fotografos = null;
        if($request->filled('mes') && $request->filled('anio')){
            $fotografos = $this->fotografosDelPeriodo((int) $request->mes, (int) $request->anio);
        }
        return view('admin.nomina', compact('meses', 'anios', 'fotografos'));
    }

    private function fotografosDelPeriodo(int $mes, int $anio)
    {
        return Fotografo::query()
            ->with('empleado.usuario.persona')
            ->whereHas('participaciones', function($q) use ($mes, $anio){
                $q->whereHas('sesion', function($q2) use ($mes, $anio){
                    $q2->where('estado','FINALIZADA')
                        ->whereYear('updated_at', $anio)
                        ->whereMonth('updated_at', $mes);
                });
            })
            ->get()
            ->map(function ($fotografo) use($mes, $anio) {
                $participaciones = $fotografo->participaciones()
                    ->whereHas('sesion', function($q) use ($mes, $anio){
                        $q->where('estado','FINALIZADA')
                            ->whereYear('updated_at', $anio)
                            ->whereMonth('updated_at', $mes);
                    })
                    ->get();

                $fotografo->sesiones_principal = $participaciones->where('rol', 'PRINCIPAL')->count();
                $fotografo->sesiones_asistente = $participaciones->where('rol', 'ASISTENTE')->count();
                $fotografo->total_sesiones = $participaciones->count();

                return $fotografo;
            });
    }
}
