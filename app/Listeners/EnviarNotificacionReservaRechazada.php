<?php

namespace App\Listeners;

use App\Events\ReservaRechazada;
use App\Mail\ReservaRechazadaCliente;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionReservaRechazada
{
    public function handle(ReservaRechazada $event): void
    {
        $reserva = $event->reserva;
        $email   = $reserva->cliente->usuario->email;

        if (!$email) return;

        Mail::to($email)->send(new ReservaRechazadaCliente($reserva));
    }
}
