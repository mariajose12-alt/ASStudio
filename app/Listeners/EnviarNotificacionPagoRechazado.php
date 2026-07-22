<?php

namespace App\Listeners;

use App\Events\PagoRechazado;
use App\Notifications\PagoRechazadoCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class EnviarNotificacionPagoRechazado implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(PagoRechazado $event): void
    {
        $pago = $event->pago;
        $usuario = $pago->reserva->cliente->usuario ?? null;

        if (!$usuario) return;

        try {
            $usuario->notify(new PagoRechazadoCliente($pago));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de pago rechazado', [
                'pago_id' => $pago->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
