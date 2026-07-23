<?php

namespace App\Listeners;

use App\Events\SeleccionConfirmada;
use App\Notifications\SeleccionConfirmadaFotografo;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionSeleccion implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SeleccionConfirmada $event): void
    {
        $usuario = $event->sesion->reserva->fotografo?->empleado?->usuario;
        if (!$usuario) return;

        try {
            $usuario->notify(new SeleccionConfirmadaFotografo($event->sesion));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de selección confirmada', [
                'sesion_id' => $event->sesion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
