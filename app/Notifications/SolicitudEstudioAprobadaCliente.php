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

    public function toWhatsApp($notifiable): ?array
    {
        return [
            'content_sid' => config('services.twilio.templates.solicitud_estudio_aprobada_cliente'),
            'variables' => [
                '1' => $notifiable->nombre,
                '2' => $this->solicitud->fecha->format('d/m'),
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new SolicitudEstudioAprobada($this->solicitud))
            ->to($notifiable->routeNotificationFor('mail'));
    }
}
