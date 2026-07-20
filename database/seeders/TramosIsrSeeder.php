<?php

namespace Database\Seeders;

use App\Models\TramoIsr;
use Illuminate\Database\Seeder;

class TramosIsrSeeder extends Seeder
{
    /**
     * Escala progresiva del ISR (DGII), vigente 2026 — mismos valores que
     * estaban como constantes ISR_* en NominaService antes de parametrizarse.
     *
     *
     */
    public function run(): void
    {
        $tramos = [
            [
                'orden'                => 1,
                'desde_anual'          => 0,
                'hasta_anual'          => 416220.00,
                'tasa'                 => 0,
                'monto_fijo_adicional' => 0,
                'fuente'               => 'DGII — escala ISR 2026 (tramo exento)',
            ],
            [
                'orden'                => 2,
                'desde_anual'          => 416220.00,
                'hasta_anual'          => 624329.00,
                'tasa'                 => 0.15,
                'monto_fijo_adicional' => 0,
                'fuente'               => 'DGII — escala ISR 2026',
            ],
            [
                'orden'                => 3,
                'desde_anual'          => 624329.00,
                'hasta_anual'          => 867123.00,
                'tasa'                 => 0.20,
                'monto_fijo_adicional' => 31216.00,
                'fuente'               => 'DGII — escala ISR 2026',
            ],
            [
                'orden'                => 4,
                'desde_anual'          => 867123.00,
                'hasta_anual'          => null,
                'tasa'                 => 0.25,
                'monto_fijo_adicional' => 79776.00,
                'fuente'               => 'DGII — escala ISR 2026',
            ],
        ];

        foreach ($tramos as $tramo) {
            TramoIsr::firstOrCreate(
                [
                    'orden'         => $tramo['orden'],
                    'vigente_desde' => '2026-02-01',
                ],
                $tramo + ['vigente_desde' => '2026-02-01']
            );
        }
    }
}
