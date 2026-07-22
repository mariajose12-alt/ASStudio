<?php

namespace App\Listeners;

use App\Events\ReservaRechazada;
use App\Notifications\ReservaRechazadaCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionReservaRechazada implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservaRechazada $event): void
    {
        $reserva = $event->reserva;
        $usuario = $reserva->cliente->usuario;
        if (!$usuario) return;

        try {
            $usuario->notify(new ReservaRechazadaCliente($reserva));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de reserva rechazada', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
