<?php

namespace App\Notifications;

use App\Mail\NuevaSolicitudEstudioSocios as NuevaSolicitudEstudioSociosMail;
use App\Models\SolicitudEstudio;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NuevaSolicitudEstudioSocios extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public SolicitudEstudio $solicitud) {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class, 'mail', 'database'];
    }

    public function toWhatsApp($notifiable): string
    {
        $fecha = $this->solicitud->fecha->format('d/m/Y');
        $hora = $this->solicitud->hora_inicio->format('H:i');

        return "¡Hola {$notifiable->nombre}! Nueva solicitud de estudio de "
            . "*{$this->solicitud->nombre} {$this->solicitud->apellido}* para el "
            . "{$fecha} a las {$hora}. "
            . route('admin.estudio.solicitudes.show', $this->solicitud->id);
    }

    public function toMail($notifiable)
    {
        return (new NuevaSolicitudEstudioSociosMail($this->solicitud, $notifiable))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        $fecha = $this->solicitud->fecha->format('d/m/Y');

        return [
            'titulo'  => 'Nueva solicitud de renta de estudio',
            'mensaje' => "{$this->solicitud->nombre} {$this->solicitud->apellido} solicitó el estudio para el {$fecha}.",
            'icono'   => 'calendar-clock',
            'url'     => route('admin.estudio.solicitudes.show', $this->solicitud->id),
        ];
    }
}
