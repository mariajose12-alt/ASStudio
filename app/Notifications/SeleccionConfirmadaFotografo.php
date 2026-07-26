<?php

namespace App\Notifications;

use App\Mail\SeleccionConfirmadaFotografo as SeleccionConfirmadaFotografoMail;
use App\Models\Sesion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class SeleccionConfirmadaFotografo extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Sesion $sesion) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new SeleccionConfirmadaFotografoMail($this->sesion))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Selección confirmada',
            'mensaje' => 'El cliente confirmó su selección de fotos. Puedes comenzar la edición.',
            'icono'   => 'checklist',
            'url'     => route('fotografo.sesiones.index'),
        ];
    }
}
