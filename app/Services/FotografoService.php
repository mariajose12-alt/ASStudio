<?php

namespace App\Services;

use App\Models\Fotografo;
use App\Models\Reserva;
use Carbon\Carbon;

class FotografoService
{
    public function metricasDashboard(Fotografo $fotografo): array
    {
        return [
            'sesionesProximas' => $fotografo->sesiones()
                ->wherePivot('estado_participacion', '!=', 'CANCELADA')
                ->where('fecha_inicio', '>=', now())
                ->orderBy('fecha_inicio')
                ->limit(5)
                ->get(),

            'totalSesiones' => $fotografo->sesiones()->count(),

            'sesionesPendientes' => $fotografo->sesiones()
                ->wherePivot('estado_participacion', 'PENDIENTE')
                ->count(),
        ];
    }

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

    public function cambiarEstadoReserva(Reserva $reserva, string $estado): void
    {
        $reserva->update(['estado' => $estado]);

        // Aquí después se disparara el evento de notificación al cliente
        // esta vaina no se ha hecho
        // ReservaEstadoCambiado::dispatch($reserva);
    }
}
