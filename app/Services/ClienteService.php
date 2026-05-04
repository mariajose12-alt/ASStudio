<?php

namespace App\Services;

use App\Models\Cliente;
use Carbon\Carbon;

class ClienteService
{
    public function metricasDashboard(Cliente $cliente): array
    {
        return [
            'totalReservas'       => $cliente->reservas()->count(),
            'reservasPendientes'  => $cliente->reservas()
                ->where('estado', 'PENDIENTE')->count(),
            'reservasCompletadas' => $cliente->reservas()
                ->where('estado', 'COMPLETADA')->count(),
            'proximasReservas'    => $cliente->reservas()
                ->with('paquete')
                ->where('fecha_inicio', '>=', now())
                ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                ->orderBy('fecha_inicio')
                ->limit(5)
                ->get(),
        ];
    }

    public function sesionesPorMes(Cliente $cliente): array
    {
        $meses   = [];
        $totales = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes     = Carbon::now()->subMonths($i);
            $meses[] = $mes->translatedFormat('M');
            $totales[] = $cliente->reservas()
                ->where('estado', 'COMPLETADA')
                ->whereYear('fecha_inicio', $mes->year)
                ->whereMonth('fecha_inicio', $mes->month)
                ->count();
        }

        return ['meses' => $meses, 'totales' => $totales];
    }
}
