<?php

namespace App\Listeners;

use App\Events\SolicitudAyudanteCreada;
use App\Mail\SolicitudAyudanteFotografo;
use App\Services\SolicitudAyudanteService;
use Illuminate\Support\Facades\Mail;

class EnviarNotificacionSolicitudAyudante
{
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

            if (! $email) {
                continue;
            }

            Mail::to($email)->send(new SolicitudAyudanteFotografo($solicitud));
        }
    }
}
