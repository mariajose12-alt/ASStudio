<?php

namespace App\Notifications;

use App\Mail\SolicitudEstudioAprobadaCliente as SolicitudEstudioAprobada;
use App\Models\SolicitudEstudio;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SolicitudEstudioAprobadaCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(public SolicitudEstudio $solicitud) {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class, 'mail'];
    }

    public function toWhatsApp($notifiable): string
    {
        return "¡Hola {$notifiable->nombre}! Tu solicitud de reserva del estudio "
            . "{$this->solicitud->fecha->format('d/m')} fue *aprobada*. "
            . "Recuerda completar el pago para confirmar tu día. ";
    }

    public function toMail($notifiable)
    {
        return (new SolicitudEstudioAprobada($this->solicitud))
            ->to($notifiable->routeNotificationFor('mail'));
    }
}
