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

    public function __construct(public Reserva $reserva) {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class, 'mail', 'database'];
    }

    public function toWhatsApp($notifiable): string
    {
        return "Hola {$notifiable->nombre}, tu reserva del "
            . "{$this->reserva->fecha_inicio->format('d/m')} no pudo ser aprobada. "
            . "Motivo de rechazo: {$this->reserva->motivo_rechazo}"
            . "Revisa los detalles e intenta agendando otra vez aquí: "
            . route('cliente.reservas.show', $this->reserva->id);
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
