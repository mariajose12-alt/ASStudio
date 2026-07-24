<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\DetalleNomina;

class ConfirmarNominasVencidas extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nomina::confirmar-vencidas';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Confirma automáticamente los detalles
    de nomina que no fueron confirmados por el fotógrafo dentro de
    3 días desde que la nomina fue calculada';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $detalles = DetalleNomina::where('estado_confirmacion', 'PENDIENTE')
            ->whereHas('nomina', function ($q) {
                $q->where('created_at', '<=', now()->subDays(3));
            })
            ->get();

        foreach ($detalles as $detalle) {

            $detalle->update([
                'estado_confirmacion' => 'CONFIRMADO',
                'confirmado_at'       => now(),
            ]);
        }

        $this->info("{$detalles->count()} detalles de nomina confirmado(s) automáticamente por vencimiento de plazo.");
    }
}
