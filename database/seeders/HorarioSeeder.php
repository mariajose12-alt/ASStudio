<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Fotografo;
use Illuminate\Support\Facades\DB;

class HorarioSeeder extends Seeder
{
    /**
     * Horario:
     *   Lunes–Viernes  → 11:00 – 22:00
     *   Sábado         → 08:00 – 22:00
     *   Domingo        → cerrado
     *
     * dia_semana sigue la convención de Carbon/PHP:
     *   0 = Domingo | 1 = Lunes | 2 = Martes | 3 = Miércoles
     *   4 = Jueves  | 5 = Viernes | 6 = Sábado
     */
    public function run(): void
    {
        $fotografo = Fotografo::latest()->first();

        $horarios = [
            ['dia_semana' => 1, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Lunes
            ['dia_semana' => 2, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Martes
            ['dia_semana' => 3, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Miércoles
            ['dia_semana' => 4, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Jueves
            ['dia_semana' => 5, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Viernes
            ['dia_semana' => 6, 'hora_inicio' => '08:00', 'hora_fin' => '22:00'], // Sábado
            // Domingo (0) no se inserta → fotógrafo no trabaja
        ];

        foreach ($horarios as $horario) {
            DB::table('horarios_fotografo')->updateOrInsert(
                [
                    'fotografo_id' => $fotografo->id,
                    'dia_semana' => $horario['dia_semana'],
                ],
                array_merge($horario, [
                    'fotografo_id' => $fotografo->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ])
            );
        }
    }
}

