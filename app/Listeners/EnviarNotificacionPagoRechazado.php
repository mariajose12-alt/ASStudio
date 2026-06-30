<?php

namespace App\Listeners;

use App\Events\PagoRechazado;
use App\Mail\PagoRechazadoCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Mail;

class EnviarNotificacionPagoRechazado
{
    public function handle(PagoRechazado $event): void
    {
        $pago = $event->pago;

        $usuario = $pago->reserva->cliente->usuario ?? null;

        if (!$usuario) return;

        Mail::to($usuario->email)->send(new PagoRechazadoCliente($pago->reserva));
    }

}
