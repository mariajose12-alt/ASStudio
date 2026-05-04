<?php

namespace App\Listeners;

use App\Events\ReservaCreada;
use App\Mail\NuevaReservaFotografo;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionNuevaReserva
{
    public function handle(ReservaCreada $event): void
    {
        $reserva   = $event->reserva;
        $fotografo = $reserva->fotografo;
        $email     = $fotografo->getUsuario()->email;

        Mail::to($email)->send(new NuevaReservaFotografo($reserva));
    }
}
