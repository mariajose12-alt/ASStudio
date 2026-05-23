<?php

namespace App\Services;

use App\DTOs\AccionReservaDTO;
use App\Events\ReservaAprobada;
use App\Events\ReservaModificada;
use App\Events\ReservaRechazada;
use App\Models\Reserva;
use Carbon\Carbon;

class FotografoReservaService
{
    public function procesarAccion(Reserva $reserva, AccionReservaDTO $dto): void
    {

        match ($dto->accion) {
            'APROBADA'    => $this->aprobar($reserva),
            'RECHAZADA'   => $this->rechazar($reserva, $dto->motivo),
            'MODIFICACION_PROPUESTA'  => $this->modificar($reserva, $dto),
            'CERRAR_SESION'  => $this->cerrarSesion($reserva),
            default      => throw new \Exception('Acción no válida.'),
        };
    }

    private function aprobar(Reserva $reserva): void
    {
        $reserva->update(['estado' => 'APROBADA']);

        // Crear la sesión automáticamente
        $reserva->sesion()->create([
            'fecha_inicio'  => $reserva->fecha_inicio,
            'fecha_fin'     => $reserva->fecha_fin,
            'lugar'         => $reserva->lugar,
            'estado'        => 'CONFIRMADA',
        ]);

        ReservaAprobada::dispatch($reserva);
    }

    private function rechazar(Reserva $reserva, ?string $motivo): void
    {
        if (!$motivo) {
            throw new \Exception('Debes indicar el motivo del rechazo.');
        }

        $reserva->update([
            'estado'          => 'RECHAZADA',
            'motivo_rechazo'  => $motivo,
        ]);

        ReservaRechazada::dispatch($reserva);
    }

    private function modificar(Reserva $reserva, AccionReservaDTO $dto): void
    {
        if (!$dto->motivo) {
            throw new \Exception('Debes indicar el motivo de la modificación.');
        }

        $data = [
            'estado'         => 'MODIFICACION_PROPUESTA',
            'motivo_rechazo' => $dto->motivo,
        ];

        // Si propone nueva fecha/hora, actualizarla
        if ($dto->nueva_fecha && $dto->nueva_hora) {
            $data['fecha_inicio'] = Carbon::parse(
                $dto->nueva_fecha . ' ' . $dto->nueva_hora
            );
        }

        $reserva->update($data);
        ReservaModificada::dispatch($reserva);
    }

    private function cerrarSesion(Reserva $reserva): void
    {
        if (!$reserva->sesion) {
            throw new \Exception('Esta reserva no tiene una sesión asociada.');
        }

        $reserva->sesion->update(['estado' => 'CERRADA']);
    }

    public function reservasPendientes(int $fotografo_id)
    {
        return Reserva::where('fotografo_id', $fotografo_id)
            ->where('estado', 'PENDIENTE')
            ->with(['cliente.usuario.persona', 'paquete'])
            ->latest()
            ->get();
    }

    public function todasLasReservas(int $fotografo_id)
    {
        return Reserva::where('fotografo_id', $fotografo_id)
            ->with(['cliente.usuario.persona', 'paquete', 'sesion'])
            ->latest()
            ->paginate(10);
    }
}
