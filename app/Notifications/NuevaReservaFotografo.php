<?php

namespace App\Notifications;

use App\Mail\NuevaReservaFotografo as NuevaReservaFotografoMail;
use App\Models\Reserva;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NuevaReservaFotografo extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(public Reserva $reserva) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new NuevaReservaFotografoMail($this->reserva))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Nueva solicitud de reserva',
            'mensaje' => "Tienes una nueva solicitud de reserva para revisar.",
            'icono'   => 'calendar-plus',
            'url'     => route('fotografo.reservas.show', $this->reserva->id),
        ];
    }
}
