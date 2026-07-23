<?php

namespace App\Listeners;

use App\Events\SolicitudAyudanteCreada;
use App\Mail\SolicitudAyudanteFotografo;
use App\Services\SolicitudAyudanteService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionSolicitudAyudante implements ShouldQueue
{
    use InteractsWithQueue;

    public function __construct(private SolicitudAyudanteService $service) {}

    public function handle(SolicitudAyudanteCreada $event): void
    {
        $solicitud = $event->solicitud;

        $disponibles = $this->service->fotografosDisponibles(
            $solicitud->sesion,
            $solicitud->solicitante
        );

        foreach ($disponibles as $fotografo) {
            $email = $fotografo->empleado?->usuario?->email;

            if (!$email) {
                continue;
            }

            try {
                Mail::to($email)->send(new SolicitudAyudanteFotografo($solicitud));
            } catch (\Throwable $e) {
                Log::error('Fallo enviando email de solicitud de ayudante', [
                    'solicitud_id' => $solicitud->id,
                    'fotografo_id' => $fotografo->id,
                    'error' => $e->getMessage(),
                ]);
            }
        }
    }
}
