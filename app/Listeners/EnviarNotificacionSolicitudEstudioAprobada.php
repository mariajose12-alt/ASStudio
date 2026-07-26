<?php

namespace App\Listeners;

use App\Events\SolicitudEstudioAprobada;
use App\Notifications\SolicitudEstudioAprobadaCliente;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Notification;

class EnviarNotificacionSolicitudEstudioAprobada implements ShouldQueue
{
    use InteractsWithQueue;

    public function handle(SolicitudEstudioAprobada $event): void
    {
        $solicitud = $event->solicitud;

        if (!$solicitud->email) return;

        try {
            Notification::route('mail', $solicitud->email)
                ->notify(new SolicitudEstudioAprobadaCliente($solicitud));
        } catch (\Throwable $e) {
            Log::error('Fallo enviando notificación de solicitud aprobada', [
                'error' => $e->getMessage(),
            ]);
        }
    }
}
