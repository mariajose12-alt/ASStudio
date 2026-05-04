<?php

namespace App\Services;

use App\Events\ReservaAprobada;
use App\Events\ReservaModificada;
use App\Events\ReservaRechazada;
use App\Models\Empleado;
use App\Models\PaqueteFotografico;
use App\Models\Catalogo;
use App\Models\Reserva;

class AdminService
{
    public function metricasDashboard(): array
    {
        return [
            'totalEmpleados'     => Empleado::count(),
            'totalPaquetes'      => PaqueteFotografico::count(),
            'totalCatalogos'     => Catalogo::count(),
            'totalReservas'      => Reserva::count(),
            'reservasPendientes' => Reserva::where('estado', 'PENDIENTE')->count(),
        ];
    }

    public function cambiarEstadoReserva(Reserva $reserva, string $estado, ?string $motivoRechazo = null): void
    {
        $reserva->update(['estado' => $estado, 'motivo_rechazo' => $motivoRechazo]);

        // Aquí se disparara el evento de notificación al cliente
        // por ahora comentado (ya revise y funciona)
        /*
        match($estado) {
            'APROBADA'               => ReservaAprobada::dispatch($reserva),
            'RECHAZADA', 'CANCELADA' => ReservaRechazada::dispatch($reserva),
            'MODIFICACION_PROPUESTA' => ReservaModificada::dispatch($reserva),
            default                  => null,
        };
        */

    }
}
