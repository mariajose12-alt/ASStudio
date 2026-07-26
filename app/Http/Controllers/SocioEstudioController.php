<?php

namespace App\Http\Controllers;

use App\Models\SolicitudEstudio;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class SocioEstudioController extends Controller
{
    public function dashboard(): View
    {
        $solicitudes = SolicitudEstudio::orderByRaw("estado = 'pendiente' desc")
            ->orderBy('fecha')
            ->paginate(10);

        $pendientesCount = SolicitudEstudio::where('estado', 'pendiente')->count();

        return view('socio.estudio', array_merge(
            compact('solicitudes', 'pendientesCount'),
            $this->estadisticas()
        ));
    }

    private function estadisticas(): array
    {
        $inicioMes    = Carbon::now()->startOfMonth();
        $finMes       = Carbon::now()->endOfMonth();
        $inicioMesAnt = Carbon::now()->subMonth()->startOfMonth();
        $finMesAnt    = Carbon::now()->subMonth()->endOfMonth();

        $totalMes    = SolicitudEstudio::whereBetween('fecha', [$inicioMes, $finMes])->count();
        $totalMesAnt = SolicitudEstudio::whereBetween('fecha', [$inicioMesAnt, $finMesAnt])->count();

        $deltaSolicitudes = $totalMesAnt > 0
            ? round((($totalMes - $totalMesAnt) / $totalMesAnt) * 100, 1)
            : 0;

        $aprobadasMes  = SolicitudEstudio::whereBetween('fecha', [$inicioMes, $finMes])->where('estado', 'aprobada')->count();
        $rechazadasMes = SolicitudEstudio::whereBetween('fecha', [$inicioMes, $finMes])->where('estado', 'rechazada')->count();
        $procesadasMes = $aprobadasMes + $rechazadasMes;

        $tasaAprobacion = $procesadasMes > 0 ? round(($aprobadasMes / $procesadasMes) * 100) : null;

        // Horas de estudio ya confirmadas este mes (suma de duración de solicitudes aprobadas)
        $horasConfirmadas = SolicitudEstudio::whereBetween('fecha', [$inicioMes, $finMes])
            ->where('estado', 'aprobada')
            ->get(['hora_inicio', 'hora_fin'])
            ->sum(function ($s) {
                return Carbon::parse($s->hora_fin)->diffInMinutes(Carbon::parse($s->hora_inicio)) / 60;
            });

        $proximasReservas = SolicitudEstudio::where('estado', 'aprobada')
            ->whereDate('fecha', '>=', Carbon::today())
            ->orderBy('fecha')
            ->orderBy('hora_inicio')
            ->limit(5)
            ->get();

        $porFinalidad = SolicitudEstudio::whereBetween('fecha', [$inicioMes, $finMes])
            ->select('finalidad', DB::raw('count(*) as total'))
            ->groupBy('finalidad')
            ->orderByDesc('total')
            ->pluck('total', 'finalidad');

        $maxFinalidad = $porFinalidad->max() ?: 1;

        // ── Insights accionables ──
        $solicitudesMes = SolicitudEstudio::whereBetween('fecha', [$inicioMes, $finMes])
            ->get(['fecha', 'hora_inicio', 'email', 'created_at']);

        $diasSemana = ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];

// Día de la semana con más solicitudes
        $porDiaSemana = $solicitudesMes->groupBy(fn ($s) => Carbon::parse($s->fecha)->dayOfWeek);
        $diaMasSolicitado = null;
        $diaMasSolicitadoPct = null;
        if ($solicitudesMes->isNotEmpty() && $porDiaSemana->isNotEmpty()) {
            $topDiaIndex = $porDiaSemana->sortByDesc(fn ($g) => $g->count())->keys()->first();
            $diaMasSolicitado = $diasSemana[$topDiaIndex];
            $diaMasSolicitadoPct = round(($porDiaSemana[$topDiaIndex]->count() / $solicitudesMes->count()) * 100);
        }

// Franja horaria más solicitada
        $porFranja = $solicitudesMes->groupBy(function ($s) {
            $hora = (int) Carbon::parse($s->hora_inicio)->format('H');
            if ($hora < 12) return 'mañana';
            if ($hora < 18) return 'tarde';
            return 'noche';
        });
        $franjaMasSolicitada = null;
        $franjaMasSolicitadaPct = null;
        if ($solicitudesMes->isNotEmpty() && $porFranja->isNotEmpty()) {
            $topFranja = $porFranja->sortByDesc(fn ($g) => $g->count())->keys()->first();
            $franjaMasSolicitada = $topFranja;
            $franjaMasSolicitadaPct = round(($porFranja[$topFranja]->count() / $solicitudesMes->count()) * 100);
        }

// % de clientes recurrentes (ya habían reservado antes de este mes)
        $pctClientesRecurrentes = null;
        $emailsMes = $solicitudesMes->pluck('email')->filter()->unique();
        if ($emailsMes->isNotEmpty()) {
            $emailsRecurrentes = SolicitudEstudio::whereIn('email', $emailsMes)
                ->where('fecha', '<', $inicioMes)
                ->distinct()
                ->pluck('email');
            $countRecurrentes = $emailsMes->intersect($emailsRecurrentes)->count();
            $pctClientesRecurrentes = round(($countRecurrentes / $emailsMes->count()) * 100);
        }

// Anticipación promedio de reserva (días entre solicitud y fecha del evento)
        $anticipacionPromedio = null;
        if ($solicitudesMes->isNotEmpty()) {
            $anticipacionPromedio = round(
                $solicitudesMes->avg(fn ($s) => Carbon::parse($s->created_at)->diffInDays(Carbon::parse($s->fecha))),
                1
            );
        }

        return compact(
            'totalMes', 'deltaSolicitudes',
            'aprobadasMes', 'rechazadasMes', 'tasaAprobacion',
            'horasConfirmadas', 'proximasReservas', 'porFinalidad', 'maxFinalidad',
            'diaMasSolicitado', 'diaMasSolicitadoPct',
            'franjaMasSolicitada', 'franjaMasSolicitadaPct',
            'pctClientesRecurrentes', 'anticipacionPromedio'
        );
    }
}
