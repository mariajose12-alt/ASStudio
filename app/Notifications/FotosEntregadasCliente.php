<?php

namespace App\Notifications;

use App\Mail\FotosEntregadasCliente as FotosEntregadasClienteMail;
use App\Models\Sesion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class FotosEntregadasCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Sesion $sesion) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new FotosEntregadasClienteMail($this->sesion))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Tus fotos están listas',
            'mensaje' => 'Las fotos editadas de tu sesión ya están disponibles.',
            'icono'   => 'photo',
            'url'     => route('cliente.galeria', $this->sesion->id),
        ];
    }
}
