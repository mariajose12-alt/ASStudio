<?php

namespace App\Listeners;

use App\Events\SeleccionConfirmada;
use App\Models\Pago;
use App\Notifications\PagoFinalPendienteCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class CrearPagoFinalAlConfirmarSeleccion implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SeleccionConfirmada $event): void
    {
        $sesion  = $event->sesion;
        $reserva = $sesion->reserva;

        // Evitar duplicados si se dispara dos veces
        $yaExiste = $reserva->pagos()
            ->where('tipo', 'FINAL')
            ->whereIn('estado', ['PENDIENTE', 'CONFIRMADO', 'RECHAZADO'])
            ->exists();

        if ($yaExiste) return;

        $montoAnticipo = $reserva->pagos()
            ->whereIn('tipo', ['ANTICIPO', 'COMPLETO'])
            ->where('estado', 'CONFIRMADO')
            ->sum('monto');

        // Si pagó completo, no hay pago final
        if ($montoAnticipo >= $reserva->precio_total) return;

        $montoFinal = round($reserva->precio_total - $montoAnticipo, 2);

        $pago = Pago::create([
            'reserva_id'     => $reserva->id,
            'cliente_id'     => $reserva->cliente_id,
            'monto'          => $montoFinal,
            'tipo'           => 'FINAL',
            'estado'         => 'PENDIENTE',
            'fecha_registro' => now(),
            // 'metodo' se define cuando el cliente sube su comprobante, igual que el anticipo
        ]);

        $usuario = $reserva->cliente->usuario ?? null;

        if ($usuario) {
            try {
                $usuario->notify(new PagoFinalPendienteCliente($pago));
            } catch (\Throwable $e) {
                Log::error('Fallo enviando notificación de pago final pendiente', [
                    'pago_id' => $pago->id,
                    'error'   => $e->getMessage(),
                ]);
            }
        }
    }
}
