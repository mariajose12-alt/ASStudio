<?php

namespace App\Notifications;

use App\Mail\SolicitudAyudanteFotografo as SolicitudAyudanteFotografoMail;
use App\Models\SolicitudAyudante;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SolicitudAyudanteFotografo extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(public SolicitudAyudante $solicitud) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new SolicitudAyudanteFotografoMail($this->solicitud))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Solicitud de ayudante',
            'mensaje' => "Se necesitan {$this->solicitud->cantidad_ayudantes} ayudante(s) para una sesión.",
            'icono'   => 'users',
            'url'     => route('fotografo.sesiones.index') . '#tab-ayudantes',
        ];
    }
}
