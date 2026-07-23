<?php

namespace App\Listeners;

use App\Events\PagoConfirmado;
use App\Notifications\PagoConfirmadoCliente;

class EnviarNotificacionPagoConfirmado
{
    public function handle(PagoConfirmado $event): void
    {
        $usuario = $event->pago->reserva->cliente->usuario ?? null;
        if (!$usuario) return;
        $usuario->notify(new PagoConfirmadoCliente($event->pago));
    }
}
