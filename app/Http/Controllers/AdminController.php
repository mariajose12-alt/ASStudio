<?php

namespace App\Http\Controllers;

use App\Events\PagoRechazado;
use App\Models\DetalleNomina;
use App\Models\Fotografo;
use App\Models\MetaMensual;
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
use Barryvdh\DomPDF\Facade\Pdf;
use App\DTOs\NominaCalculoDTO;
use App\Services\NominaService;
use App\Repositories\Contracts\NominaRepositoryInterface;
use App\Models\ConfiguracionNomina;
use App\Models\ParametroNomina;

class AdminController extends Controller
{
    private const CLAVES_PARAMETROS_LEGALES = [
        'tasa_afp_empleado',
        'tasa_sfs_empleado',
        'tasa_afp_patronal',
        'tasa_sfs_patronal',
        'tasa_riesgo_laboral',
        'tope_cotizacion_afp',
        'tope_cotizacion_sfs',
        'tope_cotizacion_riesgo_laboral',
        'monto_dependiente_adicional',
    ];

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

        // Datos para la pestaña de Configuración
        $configuracion = ConfiguracionNomina::actual();

        $parametrosLegales = collect(self::CLAVES_PARAMETROS_LEGALES)
            ->mapWithKeys(fn ($clave) => [$clave => ParametroNomina::valorVigente($clave)]);

        $topeVentasIncentivo = ParametroNomina::valorVigente('tope_ventas_incentivo');
        $porcentajeIncentivo = ParametroNomina::valorVigente('porcentaje_incentivo');

        return view('admin.nomina.nomina', compact(
            'meses', 'anios', 'fotografos', 'nominas', 'disputas',
            'configuracion', 'parametrosLegales', 'topeVentasIncentivo', 'porcentajeIncentivo'
        ));
    }

    private function fotografosDelPeriodo(int $mes, int $anio)
    {
        return Fotografo::query()
            ->with('empleado.usuario.persona')
            ->whereHas('participaciones', function($q) use ($mes, $anio){
                $q->whereHas('sesion', function($q2) use ($mes, $anio){
                    $q2->where('estado','FINALIZADA')
                        ->whereYear('fecha_finalizacion', $anio)
                        ->whereMonth('fecha_finalizacion', $mes);
                });
            })
            ->get()
            ->map(function ($fotografo) use($mes, $anio) {
                $participaciones = $fotografo->participaciones()
                    ->whereHas('sesion', function($q) use ($mes, $anio){
                        $q->where('estado','FINALIZADA')
                            ->whereYear('fecha_finalizacion', $anio)
                            ->whereMonth('fecha_finalizacion', $mes);
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

        $existente = $this->nominaRepository->porPeriodo($periodo);
        // CONFIRMADA o CERRADA: no hay checkbox que permita el recalculo, se bloquea siempre.
        if ($existente && !$existente->puedeModificarse()) {
            return redirect()
                ->route('admin.nomina', ['mes' => $mes, 'anio' => $anio])
                ->with('error', "La nómina de {$periodo} ya está {$existente->estado} y no puede recalcularse.");
        }

        // PENDIENTE o CALCULADA: se puede recalcular, pero pidiendo confirmación explícita.
        if ($existente && !$request->boolean('confirmar_recalculo')) {
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
                        ->whereYear('fecha_finalizacion', $fechaInicio->year)
                        ->whereMonth('fecha_finalizacion', $fechaInicio->month);
                })
                ->where('estado_participacion', true)
                ->with('sesion.reserva')
                ->get();

            $detalle->participaciones_detalle = $participaciones;
            return $detalle;
        });

        return view('admin.nomina.nomina-resumen', compact('nomina', 'desglose'));
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
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');

        $detalle->load('fotografo.empleado.usuario.persona', 'nomina');

        $fechaInicio = $detalle->nomina->fecha_inicio;
        $fechaFin    = $detalle->nomina->fecha_fin;

        // Participaciones actuales de este fotógrafo en el período (lo que ya cuenta)
        $participacionesActuales = ParticipacionSesion::where('fotografo_id', $detalle->fotografo_id)
            ->whereHas('sesion', function ($q) use ($fechaInicio, $fechaFin) {
                $q->where('estado', 'FINALIZADA')
                    ->whereYear('fecha_finalizacion', $fechaInicio->year)
                    ->whereMonth('fecha_finalizacion', $fechaInicio->month);
            })
            ->where('estado_participacion', true)
            ->with('sesion.reserva')
            ->get();

        // Sesiones FINALIZADA del período donde este fotógrafo NO tiene participación todavía
        // (candidatas para el dropdown de "insertar participación")
        $idsConParticipacion = $participacionesActuales->pluck('sesion_id');

        $sesionesDisponibles = \App\Models\Sesion::where('estado', 'FINALIZADA')
            ->whereYear('fecha_finalizacion', $fechaInicio->year)
            ->whereMonth('fecha_finalizacion', $fechaInicio->month)
            ->whereNotIn('id', $idsConParticipacion)
            ->with('reserva')
            ->get();

        return view('admin.nomina.nomina-disputa', compact('detalle', 'participacionesActuales', 'sesionesDisponibles'));
    }

    // Insertar una participación nueva (sesión + rol elegidos por el admin)
    public function nominaDisputaAgregarParticipacion(Request $request, DetalleNomina $detalle)
    {
        abort_unless($detalle->estaEnDisputa(), 404);
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');

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
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');
        abort_if($participacion->fotografo_id !== $detalle->fotografo_id, 403);

        $participacion->delete();

        return back()->with('success', 'Participación eliminada. Recuerda darle "Aceptar y recalcular" para aplicar el cambio.');
    }

    // Aceptar el ajuste: recalcular el detalle y volver a PENDIENTE para que el fotógrafo confirme de nuevo
    public function nominaDisputaAceptar(DetalleNomina $detalle, NominaService $nominaService)
    {
        abort_unless($detalle->estaEnDisputa(), 404);
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');

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
        abort_unless($detalle->nomina->puedeModificarse(), 403, 'Esta nómina ya no puede modificarse.');

        $detalle->update([
            'estado_confirmacion' => 'CONFIRMADO',
            'confirmado_at'        => now(),
        ]);

        return redirect()
            ->route('admin.nomina')
            ->with('success', 'Ajuste rechazado. El detalle queda confirmado con el cálculo original.');
    }

    public function nominaPagar(Nomina $nomina): RedirectResponse
    {
        if (! $nomina->estaConfirmada()) {
            return back()->with('error', 'Solo se puede marcar como pagada una nómina que esté CONFIRMADA.');
        }

        $this->nominaRepository->update($nomina->id, ['estado' => 'CERRADA']);

        return redirect()
            ->route('admin.nomina.resumen', $nomina->id)
            ->with('success', 'Nómina marcada como cerrada. El proceso ha finalizado.');
    }

    public function nominaExportarPdf(Nomina $nomina)
    {
        $nomina->load('detalles.fotografo.empleado.usuario.persona', 'creadaPor.empleado.usuario.persona');

        $fechaInicio = $nomina->fecha_inicio;
        $fechaFin    = $nomina->fecha_fin;

        $desglose = $nomina->detalles->map(function ($detalle) use ($fechaInicio) {
            $participaciones = ParticipacionSesion::where('fotografo_id', $detalle->fotografo_id)
                ->whereHas('sesion', function ($q) use ($fechaInicio) {
                    $q->where('estado', 'FINALIZADA')
                        ->whereYear('fecha_finalizacion', $fechaInicio->year)
                        ->whereMonth('fecha_finalizacion', $fechaInicio->month);
                })
                ->where('estado_participacion', true)
                ->with('sesion.reserva')
                ->get();

            $detalle->participaciones_detalle = $participaciones;
            return $detalle;
        });

        $pdf = Pdf::loadView('admin.nomina.nomina-pdf', compact('nomina', 'desglose'));

        return $pdf->download("nomina-{$nomina->periodo}.pdf");
    }

    public function nominaConfiguracionParametros(Request $request)
    {
        $request->validate([
            'valores' => 'required|array',
            'valores.*' => 'required|numeric|min:0',
        ]);

        foreach ($request->valores as $clave => $valor) {
            if (!in_array($clave, self::CLAVES_PARAMETROS_LEGALES, true)) {
                continue; // se ignora cualquier clave que no esté en la lista permitida
            }

            ParametroNomina::actualizarVersion(
                clave: $clave,
                nuevoValor: (float) $valor,
                vigenteDesde: now(),
                fuente: 'Actualizado manualmente por ' . auth()->user()->empleado->usuario->persona->nombre,
            );
        }

        return redirect()->route('admin.nomina', ['tab' => 'configuracion'])
            ->with('success', 'Parámetros actualizados correctamente. El nuevo valor aplica a partir de hoy.');
    }

    public function nominaConfiguracionIncentivos(Request $request)
    {
        $request->validate([
            'incentivos_activos'    => 'required|boolean',
            'tope_ventas_incentivo' => 'required|numeric|min:0',
            'porcentaje_incentivo'  => 'required|numeric|min:0|max:100',
        ]);

        ConfiguracionNomina::actual()->update([
            'incentivos_activos' => $request->boolean('incentivos_activos'),
            'actualizado_por_id' => auth()->user()->empleado->administrador->id,
        ]);

        ParametroNomina::actualizarVersion('tope_ventas_incentivo', (float) $request->tope_ventas_incentivo, now());
        ParametroNomina::actualizarVersion('porcentaje_incentivo', (float) $request->porcentaje_incentivo, now());

        return redirect()->route('admin.nomina', ['tab' => 'configuracion'])
            ->with('success', 'Parámetros actualizados correctamente. El nuevo valor aplica a partir de hoy.');
    }

    public function metasEdit()
    {
        $mes  = now()->month;
        $anio = now()->year;

        $meta = MetaMensual::paraMes($mes, $anio);

        return view('admin.metas.edit', compact('meta', 'mes', 'anio'));
    }

    public function metasActualizar(Request $request): RedirectResponse
    {
        $request->validate([
            'mes'             => 'required|integer|min:1|max:12',
            'anio'            => 'required|integer|min:2020|max:' . (now()->year + 1),
            'ingresos'        => 'required|numeric|min:0',
            'reservas'        => 'required|integer|min:0',
            'clientes_nuevos' => 'required|integer|min:0',
        ]);

        MetaMensual::updateOrCreate(
            ['mes' => $request->mes, 'anio' => $request->anio],
            $request->only('ingresos', 'reservas', 'clientes_nuevos')
        );

        return redirect()
            ->route('admin.dashboard')
            ->with('success', 'Metas actualizadas correctamente.');
    }
}
