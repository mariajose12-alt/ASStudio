<?php

namespace App\Listeners;

use App\Events\SolicitudEstudioCreada;
use App\Models\Usuario;
use App\Notifications\NuevaSolicitudEstudioSocios;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Log;

class NotificarSociosNuevaSolicitud implements ShouldQueue
{
    public function handle(SolicitudEstudioCreada $event): void
    {
        try {
            $socios = Usuario::whereHas('empleado', fn ($q) => $q->where('rol', 'SOCIO_ESTUDIO'))->get();

            foreach ($socios as $socio) {
                if (!$socio?->email) {
                    continue;
                }

                $socio->notify(new NuevaSolicitudEstudioSocios($event->solicitud));
            }
        } catch (\Throwable $e) {
            Log::error('Error notificando socios de nueva solicitud de estudio', [
                'solicitud_id' => $event->solicitud->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
