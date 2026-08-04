<?php

namespace App\Notifications;

use App\Mail\ReservaModificadaCliente as ReservaModificadaClienteMail;
use App\Models\Reserva;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReservaModificadaCliente extends Notification implements ShouldQueue
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
            'content_sid' => config('services.twilio.templates.reserva_modificada_cliente'),
            'variables' => [
                '1' => $notifiable->nombre,
                '2' => $this->reserva->motivo_rechazo,
                '3' => (string) $this->reserva->id, // variable del botón
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new ReservaModificadaClienteMail($this->reserva))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Cambio propuesto en tu reserva',
            'mensaje' => 'El fotógrafo propone ajustes en tu reserva. Revísalos.',
            'icono'   => 'edit',
            'url'     => route('cliente.reservas.show', $this->reserva->id),
        ];
    }
}
