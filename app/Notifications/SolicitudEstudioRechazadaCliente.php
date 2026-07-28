<?php

namespace App\Notifications;

use App\Mail\SolicitudEstudioRechazadaCliente as SolicitudEstudioRechazada;
use App\Models\SolicitudEstudio;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SolicitudEstudioRechazadaCliente extends Notification implements ShouldQueue
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
            . "{$this->solicitud->fecha->format('d/m')} fue *rechazada*. ";
    }

    public function toMail($notifiable)
    {
        return (new SolicitudEstudioRechazada($this->solicitud))
            ->to($notifiable->routeNotificationFor('mail'));
    }
}
