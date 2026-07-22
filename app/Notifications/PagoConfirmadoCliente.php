<?php

namespace App\Notifications;

use App\Mail\PagoConfirmadoCliente as PagoConfirmadoClienteMail;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;
use App\Models\Pago;

class PagoConfirmadoCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Pago $pago) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new PagoConfirmadoClienteMail($this->pago))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Pago confirmado',
            'mensaje' => 'Tu pago fue recibido y tu sesión quedó confirmada.',
            'icono'   => 'check-circle',
            'url'     => route('cliente.reservas.show', $this->pago->id),
        ];
    }
}
