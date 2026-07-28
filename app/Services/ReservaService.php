<?php

namespace App\Services;

use App\DTOs\ReservaCreateDTO;
use App\Events\ReservaCreada;
use App\Exceptions\NegocioException;
use App\Models\BloqueoEstudio;
use App\Models\Cliente;
use App\Models\Fotografo;
use App\Models\PaqueteFotografico;
use App\Repositories\Contracts\ReservaRepositoryInterface;
use App\Models\Reserva;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class ReservaService
{
    public function __construct(
        private ReservaRepositoryInterface $reservaRepository
    ) {}

    public function crearReserva(array $paso1, array $paso2, int $usuario_id, array $paso3 = []): Reserva
    {
        $fecha        = $paso2['fecha'];
        $hora         = $paso2['hora'];
        $fechaHora    = Carbon::parse("$fecha $hora");
        $fechaHoraFin = $fechaHora->copy()->addHours(2);

        if (($paso1['tipo'] ?? '') === 'ESTUDIO') {
            $bloqueado = BloqueoEstudio::solapaCon($fechaHora, $fechaHoraFin)->exists();
            if ($bloqueado) {
                throw new NegocioException('El estudio no está disponible en esa fecha y hora.');
            }
        }

        // día de la semana: 0=Domingo … 6=Sábado (igual que la tabla horarios_fotografo)
        $diaSemana  = (int) $fechaHora->dayOfWeek;
        $horaInicio = $fechaHora->format('H:i:s');
        $horaFin    = $fechaHoraFin->format('H:i:s');

        $disponibles = Fotografo::whereHas('horarios', function ($q) use ($diaSemana, $horaInicio, $horaFin) {
            // El fotógrafo trabaja ese día y la sesión completa cabe dentro de su horario
            $q->where('dia_semana',   $diaSemana)
                ->where('hora_inicio', '<=', $horaInicio)
                ->where('hora_fin',    '>=', $horaFin);
        })
            ->whereDoesntHave('reservas', function ($q) use ($fechaHora, $fechaHoraFin) {
                // No tiene reservas aprobadas/pendientes que se solapen
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                    ->where('fecha_inicio', '<', $fechaHoraFin)
                    ->where('fecha_fin',    '>', $fechaHora);
            })
            ->whereDoesntHave('participaciones', function ($q) use ($fechaHora, $fechaHoraFin) {
                // No participa en sesiones que solapen
                $q->where('estado_participacion', true)
                    ->whereHas('sesion', function ($q2) use ($fechaHora, $fechaHoraFin) {
                        $q2->where('fecha_inicio', '<', $fechaHoraFin)
                            ->where('fecha_fin',    '>', $fechaHora);
                    });
            })
            ->get();

        if ($disponibles->isEmpty()) {
            throw new NegocioException('No hay fotógrafos disponibles en esa fecha y hora.');
        }

        // Se asigna un fotógrafo principal automáticamente entre los
        // disponibles. El fotógrafo, una vez aprobada la reserva, decide si
        // necesita asistentes (ver ParticipacionSesion).
        //
        // All lo de aquí en adelante corre dentro de una transacción con
        // bloqueo por fotógrafo (pg_advisory_xact_lock) para que, si dos
        // clientes reservan al mismo tiempo, no puedan quedarse ambos con
        // el mismo fotógrafo en el mismo horario. El lock se libera solo
        // al terminar la transacción (commit o rollback).

        $paquete = PaqueteFotografico::findOrFail($paso1['paquete_id']);
        $cliente = Cliente::firstOrCreate(['usuario_id' => $usuario_id]);

        if (!empty($paso3)) {
            $cliente->update([
                'telefono'  => !empty($paso3['telefono'])  ? $paso3['telefono']  : $cliente->telefono,
                'direccion' => !empty($paso3['direccion']) ? $paso3['direccion'] : $cliente->direccion,
                'nombre'    => !empty($paso3['nombre'])    ? $paso3['nombre']    : $cliente->nombre,
            ]);
        }

        return DB::transaction(function () use ($disponibles, $fechaHora, $fechaHoraFin, $cliente, $paso1, $paso2, $paquete) {

            foreach ($disponibles->shuffle() as $fotografo) {
                // Bloqueo de aplicación por fotógrafo: si otra transacción ya
                // tomó el lock para este mismo fotógrafo, esperamos aquí
                // hasta que la suya termine (commit o rollback).
                DB::statement('SELECT pg_advisory_xact_lock(?)', [$fotografo->id]);

                // Re-confirmamos disponibilidad YA DENTRO del lock, por si
                // otra reserva se coló justo antes de que lo tomáramos.
                $sigueDisponible = !Reserva::where('fotografo_id', $fotografo->id)
                    ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                    ->where('fecha_inicio', '<', $fechaHoraFin)
                    ->where('fecha_fin',    '>', $fechaHora)
                    ->exists();

                if (! $sigueDisponible) {
                    // Ya no está libre, probamos con el siguiente candidato.
                    continue;
                }

                $dto = ReservaCreateDTO::fromSesion(
                    paso1:        $paso1,
                    paso2:        $paso2,
                    cliente_id:   $cliente->id,
                    fotografo_id: $fotografo->id,
                    precio_total: $paquete->precio_base,
                );

                $reserva = $this->reservaRepository->crear($dto);

                ReservaCreada::dispatch($reserva);

                return $reserva;
            }

            throw new NegocioException('No hay fotógrafos disponibles en esa fecha y hora.');
        });
    }


    public function reservasDelCliente(int $usuario_id)
    {
        $cliente = Cliente::where('usuario_id', $usuario_id)->first();

        if (!$cliente) return collect();

        return $this->reservaRepository->porCliente($cliente->id);
    }
}
