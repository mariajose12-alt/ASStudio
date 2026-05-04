<?php

namespace App\Listeners;

use App\Events\ReservaModificada;
use App\Mail\ReservaModificadaCliente;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionReservaModificada
{
    public function handle(ReservaModificada $event): void
    {
        $reserva = $event->reserva;
        $email   = $reserva->cliente->usuario->email;

        if (!$email) return;

        Mail::to($email)->send(new ReservaModificadaCliente($reserva));
    }
}
