<?php

namespace App\Listeners;

use App\Events\SeleccionConfirmada;
use App\Mail\SeleccionConfirmadaFotografo;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionSeleccion
{
    public function handle(SeleccionConfirmada $event): void
    {
        $email = $event->sesion->reserva->fotografo->getUsuario()->email;

        if (!$email) return;

        Mail::to($email)->send(new SeleccionConfirmadaFotografo($event->sesion));
    }
}
