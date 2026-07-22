<?php

namespace App\Listeners;

use App\Events\ReservaAprobada;
use App\Notifications\ReservaAprobadaCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionReservaAprobada implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservaAprobada $event): void
    {
        $reserva = $event->reserva;
        $usuario = $reserva->cliente->usuario;
        if (!$usuario) return;

        try {
            $usuario->notify(new ReservaAprobadaCliente($reserva));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de reserva aprobada', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
