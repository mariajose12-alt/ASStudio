<?php

namespace App\Listeners;

use App\Events\PagoConfirmado;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Notifications\PagoConfirmadoCliente;

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

        $usuario = $pago->cliente->usuario;
        if ($usuario) {
            $usuario->notify(new PagoConfirmadoCliente($pago));
        }
    }
}
