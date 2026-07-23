<?php

namespace App\Notifications;

use App\Mail\ComprobantePendienteAdmin as ComprobantePendienteAdminMail;
use App\Models\Pago;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Notification;

class ComprobantePendienteAdmin extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(public Pago $pago) {}

    public function via($notifiable): array
    {
        return ['mail', 'database'];
    }

    public function toMail($notifiable)
    {
        return (new ComprobantePendienteAdminMail($this->pago))->to($notifiable->email);
    }

    public function toDatabase($notifiable): array
    {
        return [
            'titulo'  => 'Comprobante pendiente de revisión',
            'mensaje' => "Reserva #{$this->pago->reserva_id} tiene un comprobante que requiere revisión.",
            'icono'   => 'receipt',
            'url' => route('admin.reservas.show', $this->pago->reserva_id),
        ];
    }
}
