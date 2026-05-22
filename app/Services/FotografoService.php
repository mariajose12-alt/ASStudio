<?php

namespace App\Services;

use App\Models\Fotografo;
use App\Models\Reserva;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FotografoService
{

    public function eventosCalendario(Fotografo $fotografo): array
    {
        return $fotografo->reservas()
            ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
            ->with('paquete', 'cliente.usuario.persona')
            ->get()
            ->map(fn($r) => [
                'id'    => $r->id,
                'title' => $r->paquete->nombre ?? 'Reserva',
                'start' => $r->fecha_inicio,
                'end'   => $r->fecha_fin,
                'color' => match($r->estado) {
                    'PENDIENTE' => '#a07820',
                    'APROBADA'  => '#2e7d32',
                    default     => '#888',
                },
                'extendedProps' => [
                    'cliente' => optional($r->cliente->usuario->persona)->nombre
                        . ' '
                        . optional($r->cliente->usuario->persona)->apellido,
                    'tipo'    => $r->tipo,
                    'lugar'   => $r->lugar,
                    'estado'  => $r->estado,
                ],
            ])
            ->toArray();
    }

    private Fotografo $fotografo;
    private int       $fotografoId;
    private Carbon    $hoy;
    private Carbon    $inicioMes;
    private Carbon    $finMes;
    private Carbon    $inicioMesAnt;
    private Carbon    $finMesAnt;

    public function __construct()
    {
        $this->fotografo    = Auth::user()->empleado->fotografo;
        $this->fotografoId  = $this->fotografo->id;
        $this->hoy          = Carbon::now();
        $this->inicioMes    = $this->hoy->copy()->startOfMonth();
        $this->finMes       = $this->hoy->copy()->endOfMonth();
        $this->inicioMesAnt = $this->hoy->copy()->subMonth()->startOfMonth();
        $this->finMesAnt    = $this->hoy->copy()->subMonth()->endOfMonth();
    }

    public function getData(): array
    {
        return [
            'fotografo'        => $this->fotografo->load('empleado.usuario.persona'),
            'sesionesProximas' => $this->sesionesProximas(),
            ...$this->kpis(),
            ...$this->graficos(),
        ];
    }


    // KPIs


    private function kpis(): array
    {
        $id = $this->fotografoId;

        // Base query reutilizable
        $reservas = Reserva::where('fotografo_id', $id);

        $totalReservas = (clone $reservas)->count();

        // Por estado de RESERVA
        $porEstadoReserva = (clone $reservas)
            ->select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $reservasPendientes  = (int) ($porEstadoReserva['PENDIENTE']              ?? 0)
            + (int) ($porEstadoReserva['MODIFICACION_PROPUESTA'] ?? 0);
        $reservasAprobadas   = (int) ($porEstadoReserva['APROBADA']               ?? 0);
        $reservasCanceladas  = (int) ($porEstadoReserva['CANCELADA']              ?? 0)
            + (int) ($porEstadoReserva['RECHAZADA']              ?? 0);

        // Por estado de SESION (a través de la relación reserva->sesion)
        $porEstadoSesion = Reserva::where('fotografo_id', $id)
            ->join('sesiones', 'sesiones.reserva_id', '=', 'reservas.id')
            ->select('sesiones.estado', DB::raw('COUNT(*) as total'))
            ->groupBy('sesiones.estado')
            ->pluck('total', 'sesiones.estado');

        $sesionesFinalizadas   = (int) ($porEstadoSesion['FINALIZADA']         ?? 0)
            + (int) ($porEstadoSesion['CERRADA']            ?? 0);
        $sesionesEnProceso     = (int) ($porEstadoSesion['EN_PROCESO']         ?? 0)
            + (int) ($porEstadoSesion['EN_EDICION']         ?? 0)
            + (int) ($porEstadoSesion['GALERIA_DISPONIBLE'] ?? 0);
        $sesionesConfirmadas   = (int) ($porEstadoSesion['CONFIRMADA']         ?? 0);

        // Reservas del mes actual
        $reservasEsteMes = (clone $reservas)
            ->whereBetween('fecha_inicio', [$this->inicioMes, $this->finMes])
            ->count();

        // Reservas del mes anterior (para delta)
        $reservasMesAnterior = (clone $reservas)
            ->whereBetween('fecha_inicio', [$this->inicioMesAnt, $this->finMesAnt])
            ->count();

        $deltaMes = $reservasMesAnterior > 0
            ? round((($reservasEsteMes - $reservasMesAnterior) / $reservasMesAnterior) * 100, 1)
            : 0;

        // Tasa de éxito: sesiones finalizadas / total sin canceladas
        $baseExito    = $totalReservas - $reservasCanceladas;
        $tasaExito    = $baseExito > 0 ? round($sesionesFinalizadas / $baseExito * 100) : 0;

        return compact(
            'totalReservas',
            'reservasPendientes',
            'reservasAprobadas',
            'reservasCanceladas',
            'sesionesFinalizadas',
            'sesionesEnProceso',
            'sesionesConfirmadas',
            'reservasEsteMes',
            'reservasMesAnterior',
            'deltaMes',
            'tasaExito',
        );
    }


    // GRÁFICOS


    private function graficos(): array
    {
        return [
            ...$this->reservasPorMes(),
            ...$this->distribucionEstadosSesion(),
            ...$this->tiposPorPaquete(),
            ...$this->actividadSemanal(),
        ];
    }

    // Reservas asignadas al fotógrafo — últimos 6 meses
    private function reservasPorMes(): array
    {
        $id  = $this->fotografoId;
        $raw = Reserva::where('fotografo_id', $id)
            ->select(
                DB::raw('EXTRACT(YEAR FROM fecha_inicio) as anio'),
                DB::raw('EXTRACT(MONTH FROM fecha_inicio) as mes'),
                DB::raw('COUNT(*) as total')
            )
            ->where('fecha_inicio', '>=', $this->hoy->copy()->subMonths(5)->startOfMonth())
            ->groupBy('anio', 'mes')
            ->orderBy('anio')->orderBy('mes')
            ->get();

        $labels = [];
        $data   = [];

        for ($i = 5; $i >= 0; $i--) {
            $fecha    = $this->hoy->copy()->subMonths($i);
            $labels[] = ucfirst($fecha->translatedFormat('M'));
            $anio     = (int) $fecha->format('Y');
            $mes      = (int) $fecha->format('n');
            $found    = $raw->first(fn($r) => $r->anio == $anio && $r->mes == $mes);
            $data[]   = $found ? (int) $found->total : 0;
        }

        return [
            'mesesLabels'    => $labels,
            'reservasPorMes' => $data,
        ];
    }

    // Distribución de estados de SESION del fotógrafo
    private function distribucionEstadosSesion(): array
    {
        $id  = $this->fotografoId;
        $raw = Reserva::where('fotografo_id', $id)
            ->join('sesiones', 'sesiones.reserva_id', '=', 'reservas.id')
            ->select('sesiones.estado', DB::raw('COUNT(*) as total'))
            ->groupBy('sesiones.estado')
            ->pluck('total', 'sesiones.estado');

        $estadosSesion = [
            'CONFIRMADA'        => (int) ($raw['CONFIRMADA']        ?? 0),
            'EN_PROCESO'        => (int) ($raw['EN_PROCESO']        ?? 0),
            'EN_EDICION'        => (int) ($raw['EN_EDICION']        ?? 0),
            'GALERIA_DISPONIBLE'=> (int) ($raw['GALERIA_DISPONIBLE'] ?? 0),
            'FINALIZADA'        => (int) ($raw['FINALIZADA']        ?? 0),
            'CERRADA'           => (int) ($raw['CERRADA']           ?? 0),
        ];

        return compact('estadosSesion');
    }

    // Top 5 paquetes más frecuentes del fotógrafo
    private function tiposPorPaquete(): array
    {
        $id          = $this->fotografoId;
        $tiposSesion = Reserva::where('reservas.fotografo_id', $id)
            ->join('catalogos', 'reservas.catalogo_id', '=', 'catalogos.id')
            ->select('catalogos.nombre', DB::raw('COUNT(*) as total'))
            ->whereNotNull('reservas.paquete_id')
            ->groupBy('catalogos.id', 'catalogos.nombre')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return compact('tiposSesion');
    }

    // Actividad por día de la semana (últimos 3 meses)
    private function actividadSemanal(): array
    {
        $id  = $this->fotografoId;

        $raw = Reserva::where('fotografo_id', $id)
            ->select(
                DB::raw('EXTRACT(DOW FROM reservas.fecha_inicio) as dia'),
                DB::raw('COUNT(*) as total')
            )
            ->where('reservas.fecha_inicio', '>=', $this->hoy->copy()->subMonths(3))
            ->groupBy('dia')
            ->orderBy('dia')
            ->pluck('total', 'dia');

        $semanaLabels = ['Lun', 'Mar', 'Mié', 'Jue', 'Vie', 'Sáb', 'Dom'];
        $orden = [1, 2, 3, 4, 5, 6, 0];

        $semanaData = array_map(fn($d) => (int) ($raw[$d] ?? 0), $orden);

        return compact('semanaLabels', 'semanaData');
    }


    // PRÓXIMAS SESIONES
    private function sesionesProximas()
    {
        $id = $this->fotografoId;

        return Reserva::where('fotografo_id', $id)
            ->whereIn('estado', ['APROBADA', 'PENDIENTE', 'MODIFICACION_PROPUESTA'])
            ->where('fecha_inicio', '>=', $this->hoy->copy()->startOfDay())
            ->with([
                'paquete:id,nombre',
            ])
            ->orderBy('fecha_inicio')
            ->limit(8)
            ->get();
    }

    public function cambiarEstadoReserva(Reserva $reserva, string $estado): void
    {
        $reserva->update(['estado' => $estado]);

        // Aquí después se disparará el evento de notificación al cliente
        // esta vaina no se ha hecho
        // ReservaEstadoCambiado::dispatch($reserva);
    }
}
