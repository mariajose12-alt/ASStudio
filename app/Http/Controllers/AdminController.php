<?php

namespace App\Http\Controllers;

use App\Models\Fotografo;
use App\Models\Nomina;
use App\Models\ParticipacionSesion;
use App\Models\Reserva;
use App\Services\AdminService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\DTOs\NominaCalculoDTO;
use App\Services\NominaService;

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

    public function calcularNomina(Request $request, NominaService $nominaService)
    {
        $request->validate([
            'mes' => 'required|integer|min:1|max:12',
            'anio' => 'required|integer|min:2000|max:' . now()->year,
        ]);

        $mes  =  (int) $request->mes;
        $anio =  (int) $request->anio;

        $fechaInicio = Carbon::create($anio, $mes, 1)->startOfMonth();
        $fechaFin    = $fechaInicio->copy()->endOfMonth();
        $periodo     = $fechaInicio->format('Y-m');

        $dto = new NominaCalculoDTO(
            periodo:     $periodo,
            fechaInicio: $fechaInicio,
            fechaFin:    $fechaFin,
            creadaPorId: auth()->user()->empleado->administrador->id,
        );

        $nomina = $nominaService->calcular($dto);

        return redirect ()
            ->route('admin.nomina', ['mes' => $mes, 'anio' => $anio])
            ->with('nomina_calculada', $nomina->id);
    }

    public function nominaResumen(Nomina $nomina)
    {
        $nomina->load('detalles.fotografo.empleado.usuario.persona', 'creadaPor.empleado.usuario.persona');

        // Por cada detalle (fotógrafo), traer las participaciones del mismo período
        // para mostrar el desglose de sesiones y rol que dieron ese total.
        $fechaInicio = $nomina->fecha_inicio;
        $fechaFin    = $nomina->fecha_fin;

        $desglose = $nomina->detalles->map(function ($detalle) use ($fechaInicio, $fechaFin) {
            $participaciones = ParticipacionSesion::where('fotografo_id', $detalle->fotografo_id)
                ->whereHas('sesion', function ($q) use ($fechaInicio, $fechaFin) {
                    $q->where('estado', 'FINALIZADA')
                        ->whereYear('updated_at', $fechaInicio->year)
                        ->whereMonth('updated_at', $fechaInicio->month);
                })
                ->where('estado_participacion', true)
                ->with('sesion.reserva')
                ->get();

            $detalle->participaciones_detalle = $participaciones;
            return $detalle;
        });

        return view('admin.nomina-resumen', compact('nomina', 'desglose'));
    }
}
