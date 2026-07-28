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

    public function toWhatsApp($notifiable): string
    {
        return "Hola {$notifiable->nombre}, el fotógrafo propone cambios en tu reserva "
            . "(nueva propuesta: {$this->reserva->motivo_rechazo} "
            . "{$this->reserva->fecha_inicio->format('H:i')}). "
            . "Revísalos y confirma aquí: "
            . route('cliente.reservas.show', $this->reserva->id);
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
