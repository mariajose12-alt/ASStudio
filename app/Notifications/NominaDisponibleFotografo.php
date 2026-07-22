<?php

namespace App\Notifications;

use App\Mail\NominaDisponibleFotografo as NominaDisponibleFotografoMail;
use App\Models\DetalleNomina;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class NominaDisponibleFotografo extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public DetalleNomina $detalle) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new NominaDisponibleFotografoMail($this->detalle))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Nómina disponible',
            'mensaje' => "Tu nómina de {$this->detalle->nomina->periodo} está lista para revisión.",
            'icono'   => 'cash',
            'url'     => route('fotografo.nomina.show', $this->detalle->id),
        ];
    }
}
