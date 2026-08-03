<?php

namespace App\Notifications;

use App\Mail\RecordatorioSesionCliente as RecordatorioSesionClienteMail;
use App\Notifications\Channels\WhatsAppChannel;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class RecordatorioSesionCliente extends Notification implements ShouldQueue
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
            'content_sid' => config('services.twilio.templates.recordatorio_sesion_cliente'),
            'variables' => [
                '1' => $notifiable->nombre,
                '2' => $this->reserva->fecha_inicio->format('d/m'),
                '3' => $this->reserva->fecha_inicio->format('H:i'),
            ],
        ];
    }

    public function toMail($notifiable)
    {
        return (new RecordatorioSesionClienteMail($this->reserva))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Recordatorio de sesión',
            'mensaje' => "Tu sesión es mañana a las {$this->reserva->hora}.",
            'icono'   => 'calendar-clock',
            'url'     => route('cliente.reservas.show', $this->reserva->id),
        ];
    }
}
