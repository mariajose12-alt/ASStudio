<?php

namespace App\Listeners;

use App\Events\GaleriaDisponible;
use App\Notifications\GaleriaDisponibleCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionGaleriaLista implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(GaleriaDisponible $event): void
    {
        $usuario = $event->sesion->reserva->cliente->usuario;
        if (!$usuario) return;

        try {
            $usuario->notify(new GaleriaDisponibleCliente($event->sesion));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de galería lista', [
                'sesion_id' => $event->sesion->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
