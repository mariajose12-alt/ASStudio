<?php

namespace App\Notifications;

use App\Mail\PagoRechazadoCliente as PagoRechazadoClienteMail;
use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PagoRechazadoCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Pago $pago) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new PagoRechazadoClienteMail($this->pago))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Revisa tu comprobante',
            'mensaje' => 'Tu comprobante de pago necesita ser revisado nuevamente.',
            'icono'   => 'alert-circle',
            'url'     => route('cliente.reservas.show', $this->pago->reserva_id),
        ];
    }
}
