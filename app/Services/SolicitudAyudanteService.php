<?php

namespace App\Services;

use App\Events\SolicitudAyudanteCreada;
use App\Exceptions\NegocioException;
use App\Mail\PostulacionConfirmadaFotografo;
use App\Models\Fotografo;
use App\Models\ParticipacionSesion;
use App\Models\PostulacionAyudante;
use App\Models\Sesion;
use App\Models\SolicitudAyudante;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class SolicitudAyudanteService
{

    public function crear(Sesion $sesion, Fotografo $solicitante, int $cantidad, ?string $mensaje): SolicitudAyudante
    {
        $this->validarEsPrincipal($sesion, $solicitante);

        if (! $sesion->estaActiva()) {
            throw new NegocioException('Solo puedes solicitar ayudantes para una sesión activa.');
        }

        if ($cantidad < 1) {
            throw new NegocioException('Debes solicitar al menos 1 ayudante.');
        }

        $solicitud = SolicitudAyudante::create([
            'sesion_id'                => $sesion->id,
            'fotografo_solicitante_id' => $solicitante->id,
            'cantidad_ayudantes'       => $cantidad,
            'mensaje'                  => $mensaje,
        ]);

        SolicitudAyudanteCreada::dispatch($solicitud);

        return $solicitud;
    }

    /**
     * Fotógrafos que no son el solicitante y no tienen ningún compromiso
     * (como principal o como asistente) que se solape con el horario de la sesión.
     */
    public function fotografosDisponibles(Sesion $sesion, ?Fotografo $excluir = null)
    {
        $query = Fotografo::query();

        if ($excluir) {
            $query->where('id', '!=', $excluir->id);
        }

        return $query
            ->whereDoesntHave('reservas', function ($q) use ($sesion) {
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                    ->where('fecha_inicio', '<', $sesion->fecha_fin)
                    ->where('fecha_fin', '>', $sesion->fecha_inicio);
            })
            ->whereDoesntHave('participaciones', function ($q) use ($sesion) {
                $q->where('estado_participacion', true)
                    ->whereHas('sesion', function ($qs) use ($sesion) {
                        $qs->where('id', '!=', $sesion->id)
                            ->where('fecha_inicio', '<', $sesion->fecha_fin)
                            ->where('fecha_fin', '>', $sesion->fecha_inicio);
                    });
            })
            ->with('empleado.usuario.persona')
            ->get();
    }

    public function estaDisponible(Sesion $sesion, Fotografo $fotografo): bool
    {
        return $this->fotografosDisponibles($sesion)
            ->contains('id', $fotografo->id);
    }

    public function postular(SolicitudAyudante $solicitud, Fotografo $fotografo): PostulacionAyudante
    {
        if ($fotografo->id === $solicitud->fotografo_solicitante_id) {
            throw new NegocioException('No puedes postularte a tu propia solicitud.');
        }

        if (! $solicitud->tieneCupoDisponible()) {
            throw new NegocioException('Esta solicitud ya no tiene cupos disponibles.');
        }

        if (! $this->estaDisponible($solicitud->sesion, $fotografo)) {
            throw new NegocioException('No estás disponible en el horario de esta sesión.');
        }

        $existente = $solicitud->postulaciones()->where('fotografo_id', $fotografo->id)->first();
        if ($existente) {
            throw new NegocioException('Ya te postulaste a esta solicitud.');
        }

        return $solicitud->postulaciones()->create([
            'fotografo_id' => $fotografo->id,
            'estado'       => 'PENDIENTE',
        ]);
    }

    public function confirmar(SolicitudAyudante $solicitud, PostulacionAyudante $postulacion, Fotografo $principal): void
    {
        $this->validarEsPrincipal($solicitud->sesion, $principal);

        if ($postulacion->solicitud_ayudante_id !== $solicitud->id) {
            throw new NegocioException('Esta postulación no pertenece a esta solicitud.');
        }

        if (! $postulacion->esPendiente()) {
            throw new NegocioException('Esta postulación ya fue procesada.');
        }

        if (! $solicitud->tieneCupoDisponible()) {
            throw new NegocioException('Ya no hay cupos disponibles en esta solicitud.');
        }

        DB::transaction(function () use ($solicitud, $postulacion) {
            $postulacion->update(['estado' => 'CONFIRMADO']);

            $horasTrabajadas = $solicitud->sesion
                ->participaciones()
                ->where('rol', 'PRINCIPAL')
                ->value('horas_trabajadas');

            ParticipacionSesion::create([
                'sesion_id'             => $solicitud->sesion_id,
                'fotografo_id'          => $postulacion->fotografo_id,
                'rol'                   => 'ASISTENTE',
                'porcentaje_comision'   => 0, // usa el fallback de NominaService
                'estado_participacion'  => true,
                'horas_trabajadas'      => $horasTrabajadas,
            ]);

            $solicitud->increment('cupos_confirmados');

            if ($solicitud->cuposDisponibles() === 0) {
                $solicitud->estado = 'CERRADA';
                $solicitud->save();

                // Los demás postulantes pendientes quedan rechazados automáticamente
                $solicitud->postulacionesPendientes()->update(['estado' => 'RECHAZADO']);
            }
        });

        $email = $postulacion->fotografo->empleado?->usuario?->email;
        if ($email) {
            Mail::to($email)->send(new PostulacionConfirmadaFotografo($postulacion));
        }
    }

    public function rechazar(SolicitudAyudante $solicitud, PostulacionAyudante $postulacion, Fotografo $principal): void
    {
        $this->validarEsPrincipal($solicitud->sesion, $principal);

        if (! $postulacion->esPendiente()) {
            throw new NegocioException('Esta postulación ya fue procesada.');
        }

        $postulacion->update(['estado' => 'RECHAZADO']);
    }

    public function cancelar(SolicitudAyudante $solicitud, Fotografo $principal): void
    {
        $this->validarEsPrincipal($solicitud->sesion, $principal);

        if (! $solicitud->estaAbierta()) {
            throw new NegocioException('Esta solicitud ya no está abierta.');
        }

        DB::transaction(function () use ($solicitud) {
            $solicitud->estado = 'CANCELADA';
            $solicitud->save();
            $solicitud->postulacionesPendientes()->update(['estado' => 'RECHAZADO']);
        });
    }

    private function validarEsPrincipal(Sesion $sesion, Fotografo $fotografo): void
    {
        $esPrincipal = $sesion->reserva->fotografo_id === $fotografo->id;

        if (! $esPrincipal) {
            throw new NegocioException('Solo el fotógrafo principal de la sesión puede gestionar esta solicitud.');
        }
    }
}
