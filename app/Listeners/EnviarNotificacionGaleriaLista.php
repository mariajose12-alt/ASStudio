<?php

namespace App\Listeners;

use App\Events\GaleriaDisponible;
use App\Mail\GaleriaDisponibleCliente;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionGaleriaLista
{
    public function handle(GaleriaDisponible $event): void
    {
        $email = $event->sesion->reserva->cliente->usuario->email;

        if (!$email) return;

        Mail::to($email)->send(new GaleriaDisponibleCliente($event->sesion));
    }
}
