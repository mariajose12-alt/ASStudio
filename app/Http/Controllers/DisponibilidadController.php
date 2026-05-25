<?php

namespace App\Http\Controllers;

use App\Models\Fotografo;
use App\Models\Sesion;
use Illuminate\Http\JsonResponse;
use Carbon\Carbon;

class DisponibilidadController extends Controller
{
    public function fechasOcupadas(): JsonResponse
    {
        // Horas donde NINGÚN fotógrafo está disponible
        $horas = [];

        // Todas las horas posibles del sistema
        $horasDisponibles = [
            '08:00', '09:00', '10:00', '11:00',
            '12:00', '13:00', '14:00', '15:00',
            '16:00', '17:00'
        ];

        // Obtener sesiones ya asignadas (ocupan a un fotógrafo)
        $sesiones = Sesion::whereIn('estado', [
            'PENDIENTE', 'APROBADA', 'CONFIRMADA'
        ])
            // Relacion aun no implementada
            //->with('fotografosPrincipales')
            ->with('fotografos')
            ->get();

        // Para cada sesión, verificar si quedan fotógrafos libres
        foreach ($sesiones as $sesion) {
            $fecha = Carbon::parse($sesion->fecha_inicio)->format('Y-m-d');
            $hora  = Carbon::parse($sesion->fecha_inicio)->format('H:i');

            $disponibles = $this->fotografosDisponibles($fecha, $hora);

            // Si no hay ninguno disponible, bloquear ese slot
            if ($disponibles->isEmpty()) {
                $horas[] = ['fecha' => $fecha, 'hora' => $hora];
            }
        }

        return response()->json($horas);
    }

    public function fotografosDisponibles(string $fecha, string $hora)
    {
        $fechaHora = Carbon::parse("$fecha $hora");

        return Fotografo::whereDoesntHave('sesiones', function ($q) use ($fechaHora) {
            // Fotógrafos que YA tienen sesión en ese horario
            $q->whereIn('estado', ['PENDIENTE', 'APROBADA', 'CONFIRMADA'])
                ->where('fecha_inicio', $fechaHora);
        })
            ->whereHas('agenda', function ($q) use ($fechaHora) {
                // Fotógrafos que tienen disponibilidad en ese horario
                $q//->where('disponible', true)
                    ->where('fecha_inicio', '<=', $fechaHora)
                    ->where('fecha_fin',    '>=', $fechaHora);
            })
            ->get();
    }
}
