<?php

namespace App\Http\Controllers;

use App\Models\Fotografo;
use App\Models\Reserva;
use App\Models\Sesion;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DisponibilidadController extends Controller
{
    public function fechasOcupadas(): JsonResponse
    {
        $horasDisponibles = [
            '08:00', '09:00', '10:00', '11:00',
            '12:00', '01:00', '02:00', '03:00',
            '04:00', '05:00'
        ];

        $ocupadas    = [];
        $habilitadas = [];

        $agendas = \App\Models\Agenda::all();
        foreach ($agendas as $agenda) {
            $fechaStr = Carbon::parse($agenda->fecha_inicio)->format('Y-m-d');
            if (!in_array($fechaStr, $habilitadas)) {
                $habilitadas[] = $fechaStr;
            }
        }

        $reservas = Reserva::whereIn('estado', ['PENDIENTE', 'APROBADA'])->get();

        foreach ($reservas as $reserva) {
            $fechaInicio = Carbon::parse($reserva->fecha_inicio);
            $fechaFin    = Carbon::parse($reserva->fecha_fin);
            $fecha       = $fechaInicio->format('Y-m-d');

            foreach ($horasDisponibles as $hora) {
                $slotInicio = Carbon::parse("$fecha $hora");
                $slotFin    = $slotInicio->copy()->addHours(2);

                if ($slotInicio->lt($fechaFin) && $slotFin->gt($fechaInicio)) {
                    $disponibles = $this->fotografosDisponibles($fecha, $hora);
                    if ($disponibles->isEmpty()) {
                        if (!in_array(['fecha' => $fecha, 'hora' => $hora], $ocupadas)) {
                            $ocupadas[] = ['fecha' => $fecha, 'hora' => $hora];
                        }
                    }
                }
            }
        }

        return response()->json([
            'ocupadas'    => $ocupadas,
            'habilitadas' => $habilitadas,
        ]);
    }

    public function fotografosDisponibles(string $fecha, string $hora)
    {
        $fechaHora    = Carbon::parse("$fecha $hora");
        $fechaHoraFin = $fechaHora->copy()->addHours(2);

        return Fotografo::whereHas('agenda', function ($q) use ($fechaHora) {
            // El fotógrafo trabaja ese día
            $q->where('fecha_inicio', '<=', $fechaHora)
                ->where('fecha_fin',    '>=', $fechaHora);
        })
            ->whereDoesntHave('reservas', function ($q) use ($fechaHora, $fechaHoraFin) {
                // No tiene reserva que se solape con el rango de 2 horas
                $q->whereIn('estado', ['PENDIENTE', 'APROBADA'])
                    ->where('fecha_inicio', '<', $fechaHoraFin)
                    ->where('fecha_fin',    '>', $fechaHora);
            })
            ->get();
    }
}
