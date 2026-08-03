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

    public $tries = 3;
    public $backoff = 10;

    public function __construct(public Reserva $reserva) {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class, 'mail', 'database'];
    }

    public function toWhatsApp($notifiable): ?array
    {
        return [
            'content_sid' => config('services.twilio.templates.reserva_aprobada_cliente'),
            'variables' => [
                '1' => $notifiable->nombre,
                '2' => $this->reserva->fecha_inicio->format('d/m'),
            ],
        ];
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
