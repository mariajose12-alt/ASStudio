<?php

namespace App\Listeners;

use App\Events\PagoConfirmado;
use App\Mail\PagoConfirmadoCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Mail;

class AvanzarReservaPorPagoConfirmado implements ShouldQueue
{
    public function handle(PagoConfirmado $event): void
    {
        $pago    = $event->pago;
        $reserva = $pago->reserva;

        // ANTICIPO/COMPLETO confirmado -> crea (o reutiliza) la sesión en estado CONFIRMADA.
        // FINAL confirmado -> habilita la entrega de la galería final sobre la sesión ya existente.
        match ($pago->tipo) {
            'ANTICIPO', 'COMPLETO' => $reserva->confirmar(),
            'FINAL'                => $reserva->habilitarEntregaFinal(),
            default                => null,
        };

        $email = $pago->cliente->usuario->email;

        Mail::to($email)->send(new PagoConfirmadoCliente($reserva));
    }
}
