<?php

namespace App\Services;

use App\DTOs\AccionReservaDTO;
use App\Events\ReservaAprobada;
use App\Events\ReservaModificada;
use App\Events\ReservaRechazada;
use App\Exceptions\NegocioException;
use App\Models\Fotografo;
use App\Models\Pago;
use App\Models\Reserva;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class FotografoReservaService
{
    public function procesarAccion(Reserva $reserva, AccionReservaDTO $dto, Fotografo $fotografo): void
    {
        $this->tomarSiNoTieneDueno($reserva, $fotografo);

        match ($dto->accion) {
            'APROBADA'    => $this->aprobar($reserva, $dto->duracion_horas),
            'RECHAZADA'   => $this->rechazar($reserva, $dto->motivo),
            'MODIFICACION_PROPUESTA'  => $this->modificar($reserva, $dto),
            'CERRAR_SESION'  => $this->cerrarSesion($reserva),
            default      => throw new NegocioException('Acción no válida.'),
        };
    }

    /**
     * Si la reserva está en la bolsa compartida (sin fotógrafo asignado),
     * intenta "tomarla" de forma atómica para este fotógrafo. Si otro
     * fotógrafo ya la tomó una fracción de segundo antes, el UPDATE
     * condicional afecta 0 filas y lanzamos el error correspondiente —
     * así se resuelve la condición de carrera sin locks explícitos.
     * @throws Exception
     */
    private function tomarSiNoTieneDueno(Reserva $reserva, Fotografo $fotografo): void
    {
        if ($reserva->fotografo_id === null) {
            $tomada = Reserva::where('id', $reserva->id)
                ->whereNull('fotografo_id')
                ->update(['fotografo_id' => $fotografo->id]);

            if ($tomada === 0) {
                throw new NegocioException('Esta reserva ya fue gestionada por otro fotógrafo.');
            }

            $reserva->fotografo_id = $fotografo->id;
        } elseif ($reserva->fotografo_id !== $fotografo->id) {
            throw new NegocioException('No tienes permiso para gestionar esta reserva.');
        }
    }

    public function aprobarPorAceptacionCliente(Reserva $reserva): void
    {
        $duracion = $reserva->duracion_horas_propuesta ?? 2.0;

        $this->aprobar($reserva, $duracion);

        $reserva->update([
            'motivo_rechazo'           => null,
            'duracion_horas_propuesta' => null,
        ]);
    }

    private function aprobar(Reserva $reserva, float $duracionHoras = 2.0): void
    {
        $fechaFin = Carbon::parse($reserva->fecha_inicio)->addHours($duracionHoras);

        $conflicto = Reserva::where('fotografo_id', $reserva->fotografo_id)
            ->where('id', '!=', $reserva->id)
            ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
            ->where('fecha_inicio', '<', $fechaFin)
            ->where('fecha_fin',    '>', $reserva->fecha_inicio)
            ->exists();

        if ($conflicto) {
            throw new NegocioException('La duración elegida genera un conflicto con otra reserva existente.');
        }

        DB::transaction(function () use ($reserva, $duracionHoras, $fechaFin) {
            $reserva->estado         = 'APROBADA';
            $reserva->duracion_horas = $duracionHoras;
            $reserva->fecha_fin      = $fechaFin;
            $reserva->save();

            $sesion = $reserva->sesion()->create([
                'fecha_inicio' => $reserva->fecha_inicio,
                'fecha_fin'    => $fechaFin,
                'lugar'        => $reserva->lugar,
                'estado'       => 'CONFIRMADA',
            ]);

            $sesion->participaciones()->create([
                'fotografo_id'          => $reserva->fotografo_id,
                'rol'                   => 'PRINCIPAL',
                'porcentaje_comision'   => 0,
                'estado_participacion'  => true,
                'horas_trabajadas'      => $duracionHoras,
            ]);

            $pago = new Pago([
                'reserva_id' => $reserva->id,
                'cliente_id' => $reserva->cliente_id,
                'monto'      => round($reserva->paquete->precio_base * 0.5, 2),
            ]);
            $pago->estado = 'PENDIENTE';
            $pago->tipo   = 'ANTICIPO'; // 'tipo' sí es fillable, pero si prefieres consistencia, asígnalo igual
            $pago->save();
        });

        ReservaAprobada::dispatch($reserva);
    }

    private function rechazar(Reserva $reserva, ?string $motivo): void
    {
        if (!$motivo) {
            throw new NegocioException('Debes indicar el motivo del rechazo.');
        }

        $reserva->estado         = 'RECHAZADA';
        $reserva->motivo_rechazo = $motivo;
        $reserva->save();

        ReservaRechazada::dispatch($reserva);
    }

    private function modificar(Reserva $reserva, AccionReservaDTO $dto): void
    {
        if (!$dto->motivo) {
            throw new NegocioException('Debes indicar el motivo de la modificación.');
        }

        $reserva->estado                   = 'MODIFICACION_PROPUESTA';
        $reserva->motivo_rechazo           = $dto->motivo;
        $reserva->duracion_horas_propuesta = $dto->duracion_horas;

        if ($dto->nueva_fecha && $dto->nueva_hora) {
            $reserva->fecha_inicio = Carbon::parse($dto->nueva_fecha . ' ' . $dto->nueva_hora);
        }

        $reserva->save();
        ReservaModificada::dispatch($reserva);
    }

    private function cerrarSesion(Reserva $reserva): void
    {
        if (!$reserva->sesion) {
            throw new NegocioException('Esta reserva no tiene una sesión asociada.');
        }

        $reserva->sesion->update(['estado' => 'EN_PROCESO']);
    }

    public function reservasPendientes(int $fotografo_id)
    {
        return Reserva::where(function ($q) use ($fotografo_id) {
            $q->where('fotografo_id', $fotografo_id)
                ->orWhereNull('fotografo_id');
        })
            ->where('estado', 'PENDIENTE')
            ->with(['cliente.usuario.persona', 'paquete'])
            ->latest()
            ->get();
    }

    public function todasLasReservas(int $fotografo_id)
    {
        return Reserva::where(function ($q) use ($fotografo_id) {
            $q->where('fotografo_id', $fotografo_id)
                ->orWhere(function ($q2) {
                    // Bolsa compartida: pendientes que nadie ha tomado todavía
                    $q2->whereNull('fotografo_id')->where('estado', 'PENDIENTE');
                });
        })
            ->with(['cliente.usuario.persona', 'paquete', 'sesion'])
            ->latest()
            ->paginate(10);
    }
}
