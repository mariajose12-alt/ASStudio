<?php

namespace App\Notifications;

use App\Mail\GaleriaDisponibleCliente as GaleriaDisponibleClienteMail;
use App\Models\Sesion;
use App\Notifications\Channels\WhatsAppChannel;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class GaleriaDisponibleCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Sesion $sesion, public string $tipo = 'seleccion') {}

    public function via($notifiable): array
    {
        return [WhatsAppChannel::class, 'mail', 'database'];
    }

    public function toWhatsApp($notifiable): string
    {
        return "Hola {$notifiable->nombre}, ya puedes seleccionar tus fotos favoritas. "
            . "Recuerda elegir a tiempo para que tu sesión mantenga prioridad de edición: "
            . route('cliente.galeria.index');
    }

    public function toMail($notifiable)
    {
        return (new GaleriaDisponibleClienteMail($this->sesion, $this->tipo))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => $this->tipo === 'seleccion' ? 'Tu galería está lista' : 'Tu galería final está lista',
            'mensaje' => $this->tipo === 'seleccion'
                ? 'Ya puedes seleccionar tus fotos favoritas.'
                : 'Tu galería final ya está disponible para ver.',
            'icono'   => 'photo-star',
            'url'     => route('cliente.galeria', $this->sesion->id),
        ];
    }
}
