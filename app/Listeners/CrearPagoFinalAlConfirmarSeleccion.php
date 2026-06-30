<?php

namespace App\Listeners;

use App\Events\SeleccionConfirmada;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class CrearPagoFinalAlConfirmarSeleccion
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(SeleccionConfirmada $event): void
    {
        $sesion  = $event->sesion;
        $reserva = $sesion->reserva;

        // Evitar duplicados si se dispara dos veces
        $yaExiste = $reserva->pagos()
            ->where('tipo', 'FINAL')
            ->whereIn('estado', ['PENDIENTE',  'CONFIRMADO', 'RECHAZADO'])
            ->exists();

        if ($yaExiste) return;

        $montoAnticipo = $reserva->pagos()
            ->whereIn('tipo', ['ANTICIPO', 'COMPLETO'])
            ->where('estado', 'CONFIRMADO')
            ->sum('monto');

        // Si pagó completo, no hay pago final
        if ($montoAnticipo >= $reserva->precio_total) return;

        $montoFinal = round($reserva->precio_total - $montoAnticipo, 2);

        \App\Models\Pago::create([
            'reserva_id'     => $reserva->id,
            'cliente_id'     => $reserva->cliente_id,
            'monto'          => $montoFinal,
            'tipo'           => 'FINAL',
            'estado'         => 'PENDIENTE',
            'metodo'         => 'TRANSFERENCIA',
            'fecha_registro' => now(),
        ]);
    }
}
