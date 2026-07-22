<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\MetaMensual;
use App\Models\Reserva;
use Carbon\Carbon;

class AdminService
{
    private Carbon $hoy;
    private Carbon $inicioMes;
    private Carbon $finMes;
    private Carbon $inicioAnt;
    private Carbon $finAnt;
    public function __construct()
    {
        $this->hoy       = Carbon::now();
        $this->inicioMes = $this->hoy->copy()->startOfMonth();
        $this->finMes    = $this->hoy->copy()->endOfMonth();

        $this->inicioAnt = $this->hoy->copy()->subMonth()->startOfMonth();
        $this->finAnt    = $this->hoy->copy()->subMonth()->endOfMonth();
    }

    public function getData(): array
    {
        return [
            ...$this->kpis(),
            ...$this->graficos(),
            'metas' => $this->metas(),
        ];
    }

    // KPIs
    private function kpis(): array
    {
        $ingresosMes       = $this->ingresosMes();
        $ingresosMesAnt    = $this->ingresosMesAnt();

        $totalReservas = Reserva::whereBetween(
            'fecha_inicio',
            [$this->inicioMes, $this->finMes]
        )->count();

        $totalReservasAnt = Reserva::whereBetween(
            'fecha_inicio',
            [$this->inicioAnt, $this->finAnt]
        )->count();

        $clientesNuevosMes = Cliente::whereBetween(
            'created_at',
            [$this->inicioMes, $this->finMes]
        )->count();

        $clientesNuevosAnt = Cliente::whereBetween(
            'created_at',
            [$this->inicioAnt, $this->finAnt]
        )->count();

        $crecimiento = $ingresosMesAnt > 0
            ? round((($ingresosMes - $ingresosMesAnt) / $ingresosMesAnt) * 100, 1)
            : 0;

        return [
            'ingresosMes'       => $ingresosMes,
            'ingresosMesAnt'    => $ingresosMesAnt,

            'totalReservas'     => $totalReservas,
            'totalReservasAnt'  => $totalReservasAnt,

            'clientesNuevosMes' => $clientesNuevosMes,
            'clientesNuevosAnt' => $clientesNuevosAnt,

            'crecimiento'       => $crecimiento,

            'deltaIngresos'     => $this->delta($ingresosMes, $ingresosMesAnt),
            'deltaReservas'     => $this->delta($totalReservas, $totalReservasAnt),
            'deltaClientes'     => $this->delta($clientesNuevosMes, $clientesNuevosAnt),
        ];
    }


    // GRAFICOS
    private function graficos(): array
    {
        return [
            ...$this->ingresosPorMes(),
            'estadosReservas' => $this->estadosReservas(),
            ...$this->reservasPorDia(),
            ...$this->rendimientoPaquetes(),
            ...$this->horasPico(),
        ];
    }

    // INGRESOS POR MES
    private function ingresosPorMes(): array
    {
        $fechaInicio = $this->hoy
            ->copy()
            ->subMonths(11)
            ->startOfMonth();

        $raw = Reserva::selectRaw("
                EXTRACT(YEAR FROM fecha_inicio) AS anio,
                EXTRACT(MONTH FROM fecha_inicio) AS mes,
                SUM(precio_total) AS total
            ")
            ->where('estado', 'APROBADA')
            ->where('fecha_inicio', '>=', $fechaInicio)
            ->groupByRaw("
                EXTRACT(YEAR FROM fecha_inicio),
                EXTRACT(MONTH FROM fecha_inicio)
            ")
            ->orderByRaw("
                EXTRACT(YEAR FROM fecha_inicio)
            ")
            ->orderByRaw("
                EXTRACT(MONTH FROM fecha_inicio)
            ")
            ->get();

        $rawAnt = Reserva::selectRaw("
                EXTRACT(YEAR FROM fecha_inicio) AS anio,
                EXTRACT(MONTH FROM fecha_inicio) AS mes,
                SUM(precio_total) AS total
            ")
            ->where('estado', 'APROBADA')
            ->whereBetween('fecha_inicio', [
                $this->hoy
                    ->copy()
                    ->subMonths(11)
                    ->subYear()
                    ->startOfMonth(),

                $this->hoy
                    ->copy()
                    ->subYear()
                    ->endOfMonth(),
            ])
            ->groupByRaw("
                EXTRACT(YEAR FROM fecha_inicio),
                EXTRACT(MONTH FROM fecha_inicio)
            ")
            ->orderByRaw("
                EXTRACT(YEAR FROM fecha_inicio)
            ")
            ->orderByRaw("
                EXTRACT(MONTH FROM fecha_inicio)
            ")
            ->get();

        $labels   = [];
        $actual   = [];
        $anterior = [];

        for ($i = 11; $i >= 0; $i--) {

            $fecha = $this->hoy->copy()->subMonths($i);

            $labels[] = $fecha->translatedFormat('M');

            $anio = (int) $fecha->format('Y');
            $mes  = (int) $fecha->format('n');

            $a = $raw->first(fn($r) =>
                (int)$r->anio === $anio &&
                (int)$r->mes === $mes
            );

            $actual[] = $a
                ? (float)$a->total
                : 0;

            $fAnt = $fecha->copy()->subYear();

            $b = $rawAnt->first(fn($r) =>
                (int)$r->anio === (int)$fAnt->format('Y') &&
                (int)$r->mes === (int)$fAnt->format('n')
            );

            $anterior[] = $b
                ? (float)$b->total
                : 0;
        }

        return [
            'mesesLabels'      => $labels,
            'ingresosActual'   => $actual,
            'ingresosAnterior' => $anterior,
        ];
    }


    // ESTADOS RESERVAS
    private function estadosReservas(): array
    {
        $raw = Reserva::selectRaw("
                estado,
                COUNT(*) AS total
            ")
            ->whereBetween(
                'fecha_inicio',
                [$this->inicioMes, $this->finMes]
            )
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return [
            'aprobada'   => (int) ($raw['APROBADA'] ?? 0),
            'pendiente'  => (int) ($raw['PENDIENTE'] ?? 0),
            'cancelada'  => (int) ($raw['CANCELADA'] ?? 0),
        ];
    }


    // RESERVAS POR DIA


    private function reservasPorDia(): array
    {
        $raw = Reserva::selectRaw("
                DATE(fecha_inicio) AS dia,
                COUNT(*) AS total
            ")
            ->where(
                'fecha_inicio',
                '>=',
                $this->hoy->copy()->subDays(29)->startOfDay()
            )
            ->groupByRaw("DATE(fecha_inicio)")
            ->orderByRaw("DATE(fecha_inicio)")
            ->pluck('total', 'dia');

        $labels = [];
        $data   = [];

        for ($i = 29; $i >= 0; $i--) {

            $d = $this->hoy
                ->copy()
                ->subDays($i)
                ->toDateString();

            $labels[] = Carbon::parse($d)->format('d/m');

            $data[] = (int) ($raw[$d] ?? 0);
        }

        return [
            'diasLabels' => $labels,
            'reservasDia' => $data,
        ];
    }


    // RENDIMIENTO PAQUETES


    private function rendimientoPaquetes(): array
    {
        $paquetes = Reserva::selectRaw("
                paquete_id,
                COUNT(*) AS total
            ")
            ->with('paquete:id,nombre')
            ->whereBetween(
                'fecha_inicio',
                [$this->inicioMes, $this->finMes]
            )
            ->whereNotNull('paquete_id')
            ->groupBy('paquete_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        return [
            'paquetesLabels' => $paquetes
                ->map(fn($p) => optional($p->paquete)->nombre ?? 'Sin nombre')
                ->toArray(),

            'paquetesData' => $paquetes
                ->pluck('total')
                ->map(fn($v) => (int)$v)
                ->toArray(),
        ];
    }


    // HORAS PICO


    private function horasPico(): array
    {
        $raw = Reserva::selectRaw("
                EXTRACT(HOUR FROM fecha_inicio) AS hora,
                COUNT(*) AS total
            ")
            ->whereBetween(
                'fecha_inicio',
                [$this->inicioMes, $this->finMes]
            )
            ->groupByRaw("
                EXTRACT(HOUR FROM fecha_inicio)
            ")
            ->orderByRaw("
                EXTRACT(HOUR FROM fecha_inicio)
            ")
            ->pluck('total', 'hora');

        $labels = [];
        $data   = [];

        foreach (range(8, 19) as $h) {

            $labels[] = $h . 'h';

            $data[] = (int) ($raw[$h] ?? 0);
        }

        return [
            'horasLabels' => $labels,
            'horasData'   => $data,
        ];
    }


    // HELPERS


    private function ingresosMes(): float
    {
        return (float) Reserva::whereBetween(
            'fecha_inicio',
            [$this->inicioMes, $this->finMes]
        )
            ->where('estado', 'APROBADA')
            ->sum('precio_total');
    }

    private function ingresosMesAnt(): float
    {
        return (float) Reserva::whereBetween(
            'fecha_inicio',
            [$this->inicioAnt, $this->finAnt]
        )
            ->where('estado', 'APROBADA')
            ->sum('precio_total');
    }

    private function delta(float|int $actual, float|int $anterior): float
    {
        if ($anterior == 0) {
            return 0;
        }

        return round(
            (($actual - $anterior) / $anterior) * 100,
            1
        );
    }

    private function metas(): array
    {
        $mes  = (int) $this->inicioMes->format('n');
        $anio = (int) $this->inicioMes->format('Y');

        $meta = MetaMensual::paraMes($mes, $anio);

        return [
            'ingresos'         => (float) $meta->ingresos,
            'reservas'         => (int) $meta->reservas,
            'clientes_nuevos'  => (int) $meta->clientes_nuevos,
            'meta_configurada' => MetaMensual::existeParaMes($mes, $anio),
        ];
    }
}
