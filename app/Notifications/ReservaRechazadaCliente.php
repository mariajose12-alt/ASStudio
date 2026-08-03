<?php

namespace App\Notifications;

use App\Mail\ReservaRechazadaCliente as ReservaRechazadaClienteMail;
use App\Models\Reserva;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ReservaRechazadaCliente extends Notification implements ShouldQueue
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
            'content_sid' => config('services.twilio.templates.reserva_rechazada_cliente'),
            // Este botón ("Agendar de nuevo") es estático, sin variable.
            'variables' => [
                '1' => $notifiable->nombre,
                '2' => $this->reserva->fecha_inicio->format('d/m'),
                '3' => $this->reserva->motivo_rechazo,
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new ReservaRechazadaClienteMail($this->reserva))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Reserva no aprobada',
            'mensaje' => 'Tu reserva no pudo ser aprobada. Revisa los detalles.',
            'icono'   => 'circle-x',
            'url'     => route('cliente.reservas.show', $this->reserva->id),
        ];
    }
}
