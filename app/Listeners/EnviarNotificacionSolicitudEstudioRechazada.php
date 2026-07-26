<?php

namespace App\Listeners;

use App\Events\SolicitudEstudioRechazada;
use App\Notifications\SolicitudEstudioRechazadaCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EnviarNotificacionSolicitudEstudioRechazada implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SolicitudEstudioRechazada $event): void
    {
        $solicitud = $event->solicitud;

        if (!$solicitud->email) return;

        try {
            Notification::route('mail', $solicitud->email)
                ->notify(new SolicitudEstudioRechazadaCliente($solicitud));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de solicitud aprobada', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
