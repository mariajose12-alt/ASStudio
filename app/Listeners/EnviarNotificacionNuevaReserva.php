<?php

namespace App\Listeners;

use App\Events\ReservaCreada;
use Illuminate\Support\Facades\Log;
use App\Notifications\NuevaReservaFotografo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class EnviarNotificacionNuevaReserva implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(ReservaCreada $event): void
    {
        $reserva   = $event->reserva;
        $fotografo = $reserva->fotografo;

        if (!$fotografo) {
            Log::warning("ReservaCreada sin fotógrafo asignado. reserva_id={$reserva->id}");
            return;
        }

        $usuario = $fotografo->empleado->usuario;
        if (!$usuario) return;

        try {
            $usuario->notify(new NuevaReservaFotografo($reserva));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de nueva reserva', [
                'reserva_id' => $reserva->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
