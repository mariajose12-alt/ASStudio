<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Reserva;
use App\Models\Sesion;
use App\Models\Usuario;
use Carbon\Carbon;
use Illuminate\Support\Collection;

class ClienteService
{
    /**
     * Retorna todos los datos necesarios para el dashboard del cliente.
     */
    public function getDashboardData(Usuario $usuario): array
    {
        $cliente = $usuario->cliente;

        abort_if(!$cliente, 403, 'Esta cuenta no tiene un perfil de cliente asociado.');

        return [
            'usuario'             => $this->getPerfilUsuario($usuario),
            'totalReservas'       => $this->getTotalReservas($cliente),
            'reservasPendientes'  => $this->getReservasPendientes($cliente),
            'reservasCompletadas' => $this->getSesionesCerradas($cliente),
            'proximasReservas'    => $this->getProximasReservas($cliente),
            'sesionesPorMes'      => $this->sesionesPorMes($cliente),
        ];
    }

    public function getPerfilUsuario(Usuario $usuario): Usuario
    {
        return $usuario->loadMissing('persona');
    }

    public function getTotalReservas(Cliente $cliente): int
    {
        return Reserva::where('cliente_id', $cliente->id)->count();
    }

    public function getReservasPendientes(Cliente $cliente): int
    {
        return Reserva::where('cliente_id', $cliente->id)
            ->where('estado', 'PENDIENTE')
            ->count();
    }

    public function getSesionesCerradas(Cliente $cliente): int
    {
        return Sesion::whereHas('reserva', function ($query) use ($cliente) {
            $query->where('cliente_id', $cliente->id);
        })
            ->where('estado', 'CERRADA')
            ->count();
    }

    public function getProximasReservas(Cliente $cliente, int $limit = 5): Collection
    {
        return Reserva::where('cliente_id', $cliente->id)
            ->where('fecha_inicio', '>=', Carbon::today())
            ->whereNotIn('estado', ['CANCELADA'])
            ->with('paquete')
            ->orderBy('fecha_inicio')
            ->limit($limit)
            ->get();
    }

    public function sesionesPorMes(Cliente $cliente): array
    {
        $fechaInicio = Carbon::now()->subMonths(5)->startOfMonth();

        $raw = Sesion::whereHas('reserva', function ($query) use ($cliente) {
            $query->where('cliente_id', $cliente->id);
        })
            ->where('estado', 'CERRADA')
            ->where('fecha_inicio', '>=', $fechaInicio)
            ->selectRaw('EXTRACT(YEAR FROM fecha_inicio) as anio, EXTRACT(MONTH FROM fecha_inicio) as mes, COUNT(*) as total')
            ->groupByRaw('EXTRACT(YEAR FROM fecha_inicio), EXTRACT(MONTH FROM fecha_inicio)')
            ->get();

        $meses   = [];
        $totales = [];

        for ($i = 5; $i >= 0; $i--) {
            $fecha     = Carbon::now()->subMonths($i);
            $meses[]   = $fecha->translatedFormat('M');

            $anio = (int) $fecha->format('Y');
            $mes  = (int) $fecha->format('n');

            $found = $raw->first(fn($r) => (int)$r->anio === $anio && (int)$r->mes === $mes);
            $totales[] = $found ? (int) $found->total : 0;
        }

        return ['meses' => $meses, 'totales' => $totales];
    }
}
