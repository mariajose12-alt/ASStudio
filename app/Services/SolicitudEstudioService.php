<?php

namespace App\Services;

use App\DTOs\SolicitudEstudioDTO;
use App\Events\SolicitudEstudioAprobada;
use App\Events\SolicitudEstudioCreada;
use App\Events\SolicitudEstudioRechazada;
use App\Exceptions\NegocioException;
use App\Models\BloqueoEstudio;
use App\Models\SolicitudEstudio;
use App\Models\Usuario;
use Carbon\Carbon;

class SolicitudEstudioService
{
    public function crear(SolicitudEstudioDTO $dto): SolicitudEstudio
    {
        $inicio = Carbon::parse("{$dto->fecha} {$dto->hora_inicio}");
        $fin    = Carbon::parse("{$dto->fecha} {$dto->hora_fin}");

        $this->validarDisponible($inicio, $fin);

        $solicitud = SolicitudEstudio::create([
            'nombre' => $dto->nombre,
            'apellido' => $dto->apellido,
            'email' => $dto->email,
            'telefono' => $dto->telefono,
            'invitados' => $dto->invitados,
            'finalidad' => $dto->finalidad,
            'cantidad_personas' => $dto->cantidad_personas,
            'color_fondo_adicional' => $dto->color_fondo_adicional,
            'color_fondo' => $dto->color_fondo,
            'iluminacion' => $dto->iluminacion,
            'iluminacion_otro' => $dto->iluminacion_otro,
            'fecha' => $dto->fecha,
            'hora_inicio' => $dto->hora_inicio,
            'hora_fin' => $dto->hora_fin,
        ]);

        SolicitudEstudioCreada::dispatch($solicitud);

        return $solicitud;
    }

    public function aprobar(SolicitudEstudio $solicitud, Usuario $socio, ?string $nota): void
    {
        if ($solicitud->estado !== 'pendiente') {
            throw new NegocioException('Esta solicitud ya fue procesada.');
        }

        $solicitud->estado = 'aprobada';
        $solicitud->aprobada_por = $socio->id;
        $solicitud->aprobada_at = now();
        $solicitud->nota_socio = $nota;
        $solicitud->save();

        SolicitudEstudioAprobada::dispatch($solicitud);
    }

    public function rechazar(SolicitudEstudio $solicitud, Usuario $socio, ?string $nota): void
    {
        if ($solicitud->estado !== 'pendiente') {
            throw new NegocioException('Esta solicitud ya fue procesada.');
        }

        $solicitud->estado = 'rechazada';
        $solicitud->aprobada_por = $socio->id;
        $solicitud->aprobada_at = now();
        $solicitud->nota_socio = $nota;
        $solicitud->save();

        SolicitudEstudioRechazada::dispatch($solicitud);
    }

    /**
     * Devuelve los rangos ocupados de un día, combinando solicitudes
     * pendientes/aprobadas Y bloqueos administrativos del estudio
     * (mantenimiento, uso interno, etc.) — mismo espacio físico.
     */
    public function disponibilidad(string $fecha): array
    {
        $solicitudes = SolicitudEstudio::whereDate('fecha', $fecha)
            ->whereIn('estado', ['pendiente', 'aprobada'])
            ->get(['hora_inicio', 'hora_fin'])
            ->map(fn ($s) => [
                'hora_inicio' => Carbon::parse("{$fecha} " . $s->hora_inicio->format('H:i:s'))->format('Y-m-d\TH:i:s'),
                'hora_fin'    => Carbon::parse("{$fecha} " . $s->hora_fin->format('H:i:s'))->format('Y-m-d\TH:i:s'),
            ]);

        $bloqueos = BloqueoEstudio::whereDate('inicio', $fecha)
            ->orWhereDate('fin', $fecha)
            ->get(['inicio', 'fin'])
            ->map(fn ($b) => [
                'hora_inicio' => $b->inicio->format('Y-m-d\TH:i:s'),
                'hora_fin'    => $b->fin->format('Y-m-d\TH:i:s'),
            ]);

        return $solicitudes->concat($bloqueos)
            ->sortBy('hora_inicio')
            ->values()
            ->toArray();
    }

    private function validarDisponible(Carbon $inicio, Carbon $fin): void
    {
        $solapaSolicitud = SolicitudEstudio::whereIn('estado', ['pendiente', 'aprobada'])
            ->whereDate('fecha', $inicio->toDateString())
            ->get()
            ->contains(function ($s) use ($inicio, $fin) {
                $sInicio = Carbon::parse("{$s->fecha->toDateString()} {$s->hora_inicio}");
                $sFin    = Carbon::parse("{$s->fecha->toDateString()} {$s->hora_fin}");
                return $sInicio < $fin && $sFin > $inicio;
            });

        if ($solapaSolicitud) {
            throw new NegocioException('Ese horario ya no está disponible. Por favor elige otro.');
        }

        $solapaBloqueo = BloqueoEstudio::solapaCon($inicio, $fin)->exists();

        if ($solapaBloqueo) {
            throw new NegocioException('El estudio no está disponible en esa fecha y hora.');
        }
    }
}
