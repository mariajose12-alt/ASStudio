<?php

namespace App\Notifications;

use App\Mail\ZipGaleriaListoCliente as ZipGaleriaListoClienteMail;
use App\Models\Sesion;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ZipGaleriaListoCliente extends Notification implements ShouldQueue
{
    use Queueable;

    public $tries = 3;
    public $backoff = 10;

    public function __construct(
        public Sesion $sesion,
        public string $urlDescarga,
        public string $tipo,
    ) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new ZipGaleriaListoClienteMail($this->sesion, $this->urlDescarga, $this->tipo))
            ->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        $label = $this->tipo === 'editadas' ? 'editadas' : 'originales';

        return [
            'titulo'  => "Fotos {$label} listas para descargar",
            'mensaje' => "Tu ZIP de fotos {$label} ya está disponible.",
            'icono'   => 'download',
            'url'     => $this->urlDescarga,
        ];
    }
}
