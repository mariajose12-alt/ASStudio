<?php

namespace App\Services;

use App\Models\Cliente;
use App\Models\Reserva;
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
        return [
            'usuario'             => $this->getPerfilUsuario($usuario),
            'totalReservas'       => $this->getTotalReservas($usuario),
            'reservasPendientes'  => $this->getReservasPendientes($usuario),
            'reservasCompletadas' => $this->getReservasCompletadas($usuario),
            'proximasReservas'    => $this->getProximasReservas($usuario),
            'sesionesPorMes'      => $this->sesionesPorMes($usuario->cliente),
        ];
    }

    /**
     * Retorna el usuario con su relación persona cargada.
     */
    public function getPerfilUsuario(Usuario $usuario): Usuario
    {
        return $usuario->loadMissing('persona');
    }

    /**
     * Total de reservas del cliente.
     */
    public function getTotalReservas(Usuario $usuario): int
    {
        return Reserva::where('cliente_id', $usuario->cliente->id)->count();
    }

    /**
     * Reservas en estado pendiente.
     */
    public function getReservasPendientes(Usuario $usuario): int
    {
        return Reserva::where('cliente_id', $usuario->cliente->id)
            ->where('estado', 'PENDIENTE')
            ->count();
    }

    /**
     * Reservas en estado completado.
     */
    public function getReservasCompletadas(Usuario $usuario): int
    {
        return Reserva::where('cliente_id', $usuario->cliente->id)
            ->where('estado', 'completada')
            ->count();
    }

    /**
     * Próximas reservas (desde hoy en adelante), con su paquete, ordenadas por fecha.
     */
    public function getProximasReservas(Usuario $usuario, int $limit = 5): Collection
    {
        return Reserva::where('cliente_id', $usuario->cliente->id)
            ->where('fecha_inicio', '>=', Carbon::today())
            ->whereNotIn('estado', ['cancelada'])
            ->with('paquete')
            ->orderBy('fecha_inicio')
            ->limit($limit)
            ->get();
    }
    public function sesionesPorMes(Cliente $cliente): array
    {
        $meses   = [];
        $totales = [];

        for ($i = 5; $i >= 0; $i--) {
            $mes     = Carbon::now()->subMonths($i);
            $meses[] = $mes->translatedFormat('M');
            $totales[] = $cliente->reservas()
                ->where('estado', 'completada')
                ->whereYear('fecha_inicio', $mes->year)
                ->whereMonth('fecha_inicio', $mes->month)
                ->count();
        }

        return ['meses' => $meses, 'totales' => $totales];
    }

}
