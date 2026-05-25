<?php

namespace Database\Seeders;

use App\Models\Fotografo;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class AgendaSeeder extends Seeder
{
    public function run(): void
    {
        $fotografo = Fotografo::first();

        if (!$fotografo) {
            $this->command->warn('No hay fotógrafos en la base de datos.');
            return;
        }

        // Disponible de lunes a sábado, de 8am a 6pm
        // para las próximas 4 semanas
        $inicio = Carbon::today();
        $fin    = Carbon::today()->addWeeks(4);

        $current = $inicio->copy();

        while ($current->lte($fin)) {
            // 0 = domingo, 6 = sábado — excluir domingos
            if ($current->dayOfWeek !== Carbon::SUNDAY) {
                $fotografo->agenda()->create([
                    'fecha_inicio' => $current->copy()->setTime(8, 0),
                    'fecha_fin'    => $current->copy()->setTime(18, 0),
                    'descripcion'  => 'Disponible',
                ]);
            }

            $current->addDay();
        }

        $this->command->info("Agenda creada para fotógrafo ID {$fotografo->id}.");
    }
}
