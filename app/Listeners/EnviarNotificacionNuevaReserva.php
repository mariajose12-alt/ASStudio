<?php

namespace App\Listeners;

use App\Events\ReservaCreada;
use App\Mail\NuevaReservaFotografo;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionNuevaReserva
{
    public function handle(ReservaCreada $event): void
    {
        $reserva   = $event->reserva;
        $fotografo = $reserva->fotografo;

        if (!$fotografo) {
            Log::warning("ReservaCreada sin fotógrafo asignado. reserva_id={$reserva->id}");
            return;
        }

        $email = $fotografo->getUsuario()->email;

        Mail::to($email)->send(new NuevaReservaFotografo($reserva));
    }
}
