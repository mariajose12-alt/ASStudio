<?php

namespace App\Notifications;

use App\Mail\PagoFinalPendienteCliente as PagoFinalPendienteClienteMail;
use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class PagoFinalPendienteCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(public Pago $pago) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new PagoFinalPendienteClienteMail($this->pago))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Segundo pago disponible',
            'mensaje' => 'Ya puedes realizar el pago restante de tu sesión.',
            'icono'   => 'credit-card',
            'url'     => route('cliente.pagos.index'),
        ];
    }
}
