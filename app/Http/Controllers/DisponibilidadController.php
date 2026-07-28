<?php

namespace App\Http\Controllers;

use App\Models\BloqueoEstudio;
use App\Models\Fotografo;
use App\Models\HorarioFotografo;
use App\Models\Reserva;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DisponibilidadController extends Controller
{
    public function fechasOcupadas(Request $request): JsonResponse
    {
        $esEstudio = $request->query('tipo') === 'ESTUDIO';  // ← nuevo

        $habilitadas   = [];
        $horasPorFecha = [];

        $horariosUnicos = HorarioFotografo::select('dia_semana', 'hora_inicio', 'hora_fin')
            ->distinct()
            ->get();

        $diasConHorario = $horariosUnicos->pluck('dia_semana')->unique()->values()->toArray();

        $hoy   = Carbon::today();
        $hasta = $hoy->copy()->addDays(90);

        $current = $hoy->copy();
        while ($current->lte($hasta)) {
            if (in_array((int) $current->dayOfWeek, $diasConHorario)) {
                $habilitadas[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        foreach ($habilitadas as $fecha) {
            $diaSemana = (int) Carbon::parse($fecha)->dayOfWeek;
            $slots     = $this->generarSlotsPorDia($fecha, $diaSemana, $horariosUnicos);

            $libres = [];
            foreach ($slots as $hora) {
                // Si es estudio, filtrar slots bloqueados
                if ($esEstudio && $this->estudioOcupado($fecha, $hora)) {
                    continue;
                }

                if ($this->fotografosDisponibles($fecha, $hora)->isNotEmpty()) {
                    $libres[] = $hora;
                }
            }

            if (!empty($libres)) {
                $horasPorFecha[$fecha] = $libres;
            } else {
                $habilitadas = array_values(array_filter($habilitadas, fn($f) => $f !== $fecha));
            }
        }

        return response()->json([
            'habilitadas'   => array_values($habilitadas),
            'horasPorFecha' => $horasPorFecha,
        ]);
    }
    private function estudioOcupado(string $fecha, string $hora): bool
    {
        $inicio = Carbon::parse("$fecha $hora");
        $fin    = $inicio->copy()->addHours(2);

        return BloqueoEstudio::solapaCon($inicio, $fin)->exists();
    }

    private function generarSlotsPorDia(string $fecha, int $diaSemana, $horariosUnicos): array
    {
        // sin cambios
        $slots = [];
        $horariosDia = $horariosUnicos->where('dia_semana', $diaSemana);

        foreach ($horariosDia as $horario) {
            $inicio    = Carbon::parse("$fecha {$horario->hora_inicio}");
            $finMaximo = Carbon::parse("$fecha {$horario->hora_fin}")->subHours(2);

            $slot = $inicio->copy();
            while ($slot->lte($finMaximo)) {
                $horaStr = $slot->format('H:i');
                if (!in_array($horaStr, $slots)) {
                    $slots[] = $horaStr;
                }
                $slot->addMinutes(30);
            }
        }

        sort($slots);
        return $slots;
    }

    public function fotografosDisponibles(string $fecha, string $hora)
    {
        // sin cambios
        $fechaHora    = Carbon::parse("$fecha $hora");
        $fechaHoraFin = $fechaHora->copy()->addHours(2);
        $diaSemana    = (int) $fechaHora->dayOfWeek;
        $horaInicio   = $fechaHora->format('H:i:s');
        $horaFin      = $fechaHoraFin->format('H:i:s');

        return Fotografo::whereHas('horarios', function ($q) use ($diaSemana, $horaInicio, $horaFin) {
            $q->where('dia_semana',   $diaSemana)
                ->where('hora_inicio', '<=', $horaInicio)
                ->where('hora_fin',    '>=', $horaFin);
        })
            ->whereDoesntHave('reservas', function ($q) use ($fechaHora, $fechaHoraFin) {
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                    ->where('fecha_inicio', '<', $fechaHoraFin)
                    ->where('fecha_fin',    '>', $fechaHora);
            })
            ->get();
    }

    /**
     * Igual que fechasOcupadas(), pero para UN fotógrafo específico — se usa
     * cuando ya hay una reserva asignada a ese fotógrafo y se le va a
     * proponer (o el cliente va a elegir) una nueva fecha/hora para ELLA.
     * Excluye del cálculo la propia reserva que se está reprogramando.
     */
    public function fechasOcupadasFotografo(Request $request, Fotografo $fotografo): JsonResponse
    {
        $excluirReservaId = $request->query('excluir_reserva');

        $habilitadas   = [];
        $horasPorFecha = [];

        $horarios = HorarioFotografo::where('fotografo_id', $fotografo->id)->get();

        $diasConHorario = $horarios->pluck('dia_semana')->unique()->values()->toArray();

        $hoy   = Carbon::today();
        $hasta = $hoy->copy()->addDays(90);

        $current = $hoy->copy();
        while ($current->lte($hasta)) {
            if (in_array((int) $current->dayOfWeek, $diasConHorario)) {
                $habilitadas[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        foreach ($habilitadas as $fecha) {
            $diaSemana = (int) Carbon::parse($fecha)->dayOfWeek;
            $slots     = $this->generarSlotsPorDia($fecha, $diaSemana, $horarios);

            $libres = [];
            foreach ($slots as $hora) {
                if ($this->fotografoLibre($fotografo->id, $fecha, $hora, $excluirReservaId)) {
                    $libres[] = $hora;
                }
            }

            if (!empty($libres)) {
                $horasPorFecha[$fecha] = $libres;
            } else {
                $habilitadas = array_values(array_filter($habilitadas, fn($f) => $f !== $fecha));
            }
        }

        return response()->json([
            'habilitadas'   => array_values($habilitadas),
            'horasPorFecha' => $horasPorFecha,
        ]);
    }

    private function fotografoLibre(int $fotografoId, string $fecha, string $hora, ?string $excluirReservaId): bool
    {
        $fechaHora    = Carbon::parse("$fecha $hora");
        $fechaHoraFin = $fechaHora->copy()->addHours(2);

        return !Reserva::where('fotografo_id', $fotografoId)
            ->when($excluirReservaId, fn ($q) => $q->where('id', '!=', $excluirReservaId))
            ->whereIn('estado', ['PENDIENTE', 'APROBADA'])
            ->where('fecha_inicio', '<', $fechaHoraFin)
            ->where('fecha_fin',    '>', $fechaHora)
            ->exists();
    }
}
