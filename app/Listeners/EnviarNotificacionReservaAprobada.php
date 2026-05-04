<?php

namespace App\Listeners;

use App\Events\ReservaAprobada;
use App\Mail\ReservaAprobadaCliente;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionReservaAprobada
{
    public function handle(ReservaAprobada $event): void
    {
        $reserva = $event->reserva;
        $email   = $reserva->cliente->usuario->email;

        if (!$email) return;

        Mail::to($email)->send(new ReservaAprobadaCliente($reserva));
    }
}
