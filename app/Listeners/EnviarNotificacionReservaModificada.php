<?php

namespace App\Listeners;

use App\Events\ReservaModificada;
use App\Notifications\ReservaModificadaCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionReservaModificada implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservaModificada $event): void
    {
        $reserva = $event->reserva;
        $usuario = $reserva->cliente->usuario;
        if (!$usuario) return;

        try {
            $usuario->notify(new ReservaModificadaCliente($reserva));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de reserva modificada', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
