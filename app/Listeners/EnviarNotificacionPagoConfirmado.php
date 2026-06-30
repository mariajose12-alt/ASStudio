<?php

namespace App\Listeners;

use App\Events\PagoConfirmado;
use App\Mail\PagoConfirmadoCliente;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionPagoConfirmado
{
    public function handle(PagoConfirmado $event): void
    {
        $email = $event->pago->reserva->cliente->usuario->email ?? null;

        if (!$email) return;

        Mail::to($email)->send(new PagoConfirmadoCliente($event->pago->reserva));
    }
}
