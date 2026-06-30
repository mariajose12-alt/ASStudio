<?php

namespace App\Http\Controllers;

use App\Events\PagoRechazado;
use App\Models\DetalleNomina;
use App\Models\Fotografo;
use App\Models\Nomina;
use App\Models\ParticipacionSesion;
use App\Events\PagoConfirmado;
use App\Models\Reserva;
use App\Services\AdminService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\Pago;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

use App\DTOs\NominaCalculoDTO;
use App\Services\NominaService;
use App\Repositories\Contracts\NominaRepositoryInterface;

class AdminController extends Controller
{
    public function __construct(
        private AdminService $adminService,
         private NominaRepositoryInterface $nominaRepository
    ) {}

    public function dashboard()
    {
        return view('admin.dashboard', $this->adminService->getData());
    }

    /**
     * Lista los pagos con comprobante subido, pendientes de revisión.
     */
    public function pagosIndex(): View
    {
        $pagos = Pago::whereNotNull('comprobante_id')
            ->where('estado', 'EN_REVISION')
            ->with(['comprobante', 'reserva.cliente.usuario.persona'])
            ->latest('fecha_registro')
            ->paginate(15);

        return view('admin.pagos.index', compact('pagos'));
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

    /**
     * Pantalla de revisión de un pago específico (comprobante, datos OCR, monto esperado).
     */
    public function pagosRevisar(Pago $pago): View
    {
        $pago->load(['comprobante', 'reserva.cliente.usuario.persona', 'reserva.paquete']);

        return view('admin.pagos.revisar', compact('pago'));
    }

    /**
     * Aprueba el pago. Dispara PagoConfirmado -> crea/activa la sesión y avisa al cliente.
     */
    public function pagosAprobar(Pago $pago): RedirectResponse
    {
        if (! $pago->estaEnRevision()) {
            return back()->with('error', 'Este pago ya fue procesado anteriormente.');
        }

        $pago->aprobar();

        event(new PagoConfirmado($pago));

        return redirect()
            ->route('admin.pagos.index')
            ->with('success', 'Pago aprobado correctamente.');
    }

    /**
     * Rechaza el pago con un motivo obligatorio. Dispara PagoRechazado -> avisa al cliente.
     */
    public function pagosRechazar(Request $request, Pago $pago): RedirectResponse
    {
        if (! $pago->estaEnRevision()) {
            return back()->with('error', 'Este pago ya fue procesado anteriormente.');
        }

        $request->validate([
            'motivo' => ['required', 'string', 'max:500'],
        ], [
            'motivo.required' => 'Debes indicar el motivo del rechazo para que el cliente sepa qué corregir.',
        ]);

        $pago->rechazar($request->input('motivo'));

        event(new PagoRechazado($pago));

        return redirect()
            ->route('admin.pagos.index')
            ->with('success', 'Pago rechazado. Se notificó al cliente.');
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

        // Si no vienen mes/año en la URL, usar el mes/año actual por defecto
        // para que la tabla cargue de inmediato al entrar a la página.
        $mes  = $request->filled('mes')  ? (int) $request->mes  : now()->month;
        $anio = $request->filled('anio') ? (int) $request->anio : now()->year;

        $fotografos = $this->fotografosDelPeriodo($mes, $anio);

        $nominas = $this->nominaRepository->all();

        $disputas = DetalleNomina::where('estado_confirmacion', 'EN_DISPUTA')
            ->with('fotografo.empleado.usuario.persona', 'nomina')
            ->latest('id')
            ->get();

        return view('admin.nomina', compact('meses', 'anios', 'fotografos', 'nominas', 'disputas'));
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

        //Si el período ya fue procesado y el admin no confirmó
        //explícitamente el recálculo, frenar y pedir confirmación.
        if($this->nominaRepository->periodoYaProcesado($periodo) && !$request->boolean('confirmar_recalculo'))
        {
            return redirect()
                ->route('admin.nomina', ['mes' => $mes, 'anio' => $anio])
                ->with('periodo_ya_procesado', $periodo);
        }

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

    public function nominaConfirmar(Nomina $nomina)
    {
        $nomina->load('detalles');

        if($nomina->estado !== 'CALCULADA')
        {
            return back()->with('error', 'Solo se puede confirmar una nómina que esté en estado CALCULADA');
        }

        if($nomina->tieneDisputasPendientes())
        {
            return back()->with('error', 'No puedes confirmar la nómina porque hay detalles en disputa. Resuélvelos antes de continuar.');
        }

        // se hará un job para marcar como confirmada pasada 3 días después de su envío.
        if(!$nomina->todosFotografosConfirmaron())
        {
            return back()->with('error', 'No puedes confirmar la nómina hasta que todos los fotógrafos hayan confirmado su resumen individual.');
        }

        $this->nominaRepository->update($nomina->id, ['estado' => 'CONFIRMADA']);

        return redirect ()
            ->route('admin.nomina.resumen', $nomina->id)
            ->with('sucess', 'Nómina confirmada correctamente, El historial ha sido actualizado.');
    }

    // Pantalla de resolución individual de una disputa
    public function nominaDisputaShow(DetalleNomina $detalle)
    {
        abort_unless($detalle->estaEnDisputa(), 404);

        $detalle->load('fotografo.empleado.usuario.persona', 'nomina');

        $fechaInicio = $detalle->nomina->fecha_inicio;
        $fechaFin    = $detalle->nomina->fecha_fin;

        // Participaciones actuales de este fotógrafo en el período (lo que ya cuenta)
        $participacionesActuales = ParticipacionSesion::where('fotografo_id', $detalle->fotografo_id)
            ->whereHas('sesion', function ($q) use ($fechaInicio, $fechaFin) {
                $q->where('estado', 'FINALIZADA')
                    ->whereYear('updated_at', $fechaInicio->year)
                    ->whereMonth('updated_at', $fechaInicio->month);
            })
            ->where('estado_participacion', true)
            ->with('sesion.reserva')
            ->get();

        // Sesiones FINALIZADA del período donde este fotógrafo NO tiene participación todavía
        // (candidatas para el dropdown de "insertar participación")
        $idsConParticipacion = $participacionesActuales->pluck('sesion_id');

        $sesionesDisponibles = \App\Models\Sesion::where('estado', 'FINALIZADA')
            ->whereYear('updated_at', $fechaInicio->year)
            ->whereMonth('updated_at', $fechaInicio->month)
            ->whereNotIn('id', $idsConParticipacion)
            ->with('reserva')
            ->get();

        return view('admin.nomina-disputa', compact('detalle', 'participacionesActuales', 'sesionesDisponibles'));
    }

    // Insertar una participación nueva (sesión + rol elegidos por el admin)
    public function nominaDisputaAgregarParticipacion(Request $request, DetalleNomina $detalle)
    {
        abort_unless($detalle->estaEnDisputa(), 404);

        $request->validate([
            'sesion_id' => 'required|exists:sesiones,id',
            'rol'       => 'required|in:PRINCIPAL,ASISTENTE',
        ]);

        ParticipacionSesion::create([
            'sesion_id'            => $request->sesion_id,
            'fotografo_id'         => $detalle->fotografo_id,
            'rol'                  => $request->rol,
            'porcentaje_comision'  => 0,
            'estado_participacion' => true,
            'horas_trabajadas'     => 0,
        ]);

        return back()->with('success', 'Participación agregada. Recuerda darle "Aceptar y recalcular" para aplicar el cambio.');
    }

    // Eliminar una participación existente
    public function nominaDisputaEliminarParticipacion(DetalleNomina $detalle, ParticipacionSesion $participacion)
    {
        abort_unless($detalle->estaEnDisputa(), 404);
        abort_if($participacion->fotografo_id !== $detalle->fotografo_id, 403);

        $participacion->delete();

        return back()->with('success', 'Participación eliminada. Recuerda darle "Aceptar y recalcular" para aplicar el cambio.');
    }

    // Aceptar el ajuste: recalcular el detalle y volver a PENDIENTE para que el fotógrafo confirme de nuevo
    public function nominaDisputaAceptar(DetalleNomina $detalle, NominaService $nominaService)
    {
        abort_unless($detalle->estaEnDisputa(), 404);

        $nominaService->recalcularDetalle($detalle);

        $detalle->update([
            'estado_confirmacion'   => 'PENDIENTE',
            'observacion_fotografo' => null,
        ]);

        return redirect()
            ->route('admin.nomina')
            ->with('success', 'Ajuste aceptado. El detalle fue recalculado y vuelve a estar pendiente de confirmación del fotógrafo.');
    }

    // Rechazar el ajuste: el cálculo original era correcto, se confirma tal cual
    public function nominaDisputaRechazar(DetalleNomina $detalle)
    {
        abort_unless($detalle->estaEnDisputa(), 404);

        $detalle->update([
            'estado_confirmacion' => 'CONFIRMADO',
            'confirmado_at'        => now(),
        ]);

        return redirect()
            ->route('admin.nomina')
            ->with('success', 'Ajuste rechazado. El detalle queda confirmado con el cálculo original.');
    }
}
