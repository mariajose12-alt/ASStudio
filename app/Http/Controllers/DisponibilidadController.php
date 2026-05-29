<?php

namespace App\Http\Controllers;

use App\Models\Fotografo;
use App\Models\HorarioFotografo;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DisponibilidadController extends Controller
{
    public function fechasOcupadas(): JsonResponse
    {
        $habilitadas         = [];
        $horasPorFecha       = []; // ['Y-m-d' => ['08:00', '09:00', ...]]

        // Obtener todos los slots disponibles según los horarios de los fotógrafos
        // dia_semana: 0=Domingo … 6=Sábado (igual que Carbon::dayOfWeek)
        $horariosUnicos = HorarioFotografo::select('dia_semana', 'hora_inicio', 'hora_fin')
            ->distinct()
            ->get();

        // Generar las fechas habilitadas: los próximos 90 días que tengan
        // al menos un fotógrafo con horario ese día de la semana
        $diasConHorario = $horariosUnicos->pluck('dia_semana')->unique()->values()->toArray();

        $hoy   = Carbon::today();
        $hasta = $hoy->copy()->addDays(90); //Se puede reservar con 3 meses de anticipacion

        $current = $hoy->copy();
        while ($current->lte($hasta)) {
            if (in_array((int) $current->dayOfWeek, $diasConHorario)) {
                $habilitadas[] = $current->format('Y-m-d');
            }
            $current->addDay();
        }

        // Para cada fecha habilitada, guardar solo los slots que tienen
        // al menos un fotógrafo disponible
        foreach ($habilitadas as $fecha) {
            $diaSemana = (int) Carbon::parse($fecha)->dayOfWeek;
            $slots     = $this->generarSlotsPorDia($fecha, $diaSemana, $horariosUnicos);

            $libres = [];
            foreach ($slots as $hora) {
                if ($this->fotografosDisponibles($fecha, $hora)->isNotEmpty()) {
                    $libres[] = $hora;
                }
            }

            // Si no quedó ninguna hora libre, la fecha no sirve de nada
            if (!empty($libres)) {
                $horasPorFecha[$fecha] = $libres;
            } else {
                // Quitar la fecha de habilitadas si está completamente llena
                $habilitadas = array_values(array_filter($habilitadas, fn($f) => $f !== $fecha));
            }
        }

        return response()->json([
            'habilitadas'   => array_values($habilitadas),
            'horasPorFecha' => $horasPorFecha,
        ]);
    }

    /**
     * Genera los slots de hora (formato 'H:i') válidos para un día dado,
     * respetando los horarios de los fotógrafos y dejando 2h para la sesión.
     */
    private function generarSlotsPorDia(string $fecha, int $diaSemana, $horariosUnicos): array
    {
        $slots = [];

        $horariosDia = $horariosUnicos->where('dia_semana', $diaSemana);

        foreach ($horariosDia as $horario) {
            $inicio = Carbon::parse("$fecha {$horario->hora_inicio}");
            // El último slot válido debe dejar 2h antes del fin del horario
            $finMaximo = Carbon::parse("$fecha {$horario->hora_fin}")->subHours(2);

            $slot = $inicio->copy();
            while ($slot->lte($finMaximo)) {
                $horaStr = $slot->format('H:i');
                if (!in_array($horaStr, $slots)) {
                    $slots[] = $horaStr;
                }
                $slot->addHour();
            }
        }

        sort($slots);

        return $slots;
    }

    /**
     * Devuelve los fotógrafos disponibles para una fecha y hora dadas.
     * Misma lógica que ReservaService: verifica horario semanal y no solapamiento de reservas.
     */
    public function fotografosDisponibles(string $fecha, string $hora)
    {
        $fechaHora    = Carbon::parse("$fecha $hora");
        $fechaHoraFin = $fechaHora->copy()->addHours(2);

        // día de la semana: 0=Domingo … 6=Sábado
        $diaSemana  = (int) $fechaHora->dayOfWeek;
        $horaInicio = $fechaHora->format('H:i:s');
        $horaFin    = $fechaHoraFin->format('H:i:s');

        return Fotografo::whereHas('horarios', function ($q) use ($diaSemana, $horaInicio, $horaFin) {
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
            ->get();
    }
}
