<?php

namespace App\Notifications;

use App\Mail\ReservaAprobadaCliente as ReservaAprobadaClienteMail;
use App\Models\Reserva;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReservaAprobadaCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Reserva $reserva) {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class, 'mail', 'database'];
    }

    public function toWhatsApp($notifiable): string
    {
        return "¡Hola {$notifiable->nombre}! Tu reserva del "
            . "{$this->reserva->fecha_inicio->format('d/m')} fue *aprobada*. "
            . "Recuerda completar el pago para confirmar tu día. "
            . route('cliente.reservas.show', $this->reserva->id);
    }

    public function toMail($notifiable)
    {
        return (new ReservaAprobadaClienteMail($this->reserva))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => '¡Tu reserva fue aprobada!',
            'mensaje' => 'Tu solicitud de sesión fue aprobada por el fotógrafo.',
            'icono'   => 'circle-check',
            'url'     => route('cliente.reservas.show', $this->reserva->id),
        ];
    }
}
