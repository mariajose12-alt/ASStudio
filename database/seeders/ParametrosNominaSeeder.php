<?php

namespace Database\Seeders;

use App\Models\ParametroNomina;
use Illuminate\Database\Seeder;

class ParametrosNominaSeeder extends Seeder
{
    /**
     * Valores oficiales DGII/TSS vigentes, validados contra fuentes primarias
     * (presidencia.gob.do / tss.gob.do) en julio 2026.
     *
     * IMPORTANTE: los topes de cotización SFS/AFP y las fechas exactas de
     * vigencia de cada tramo deben reconfirmarse contra la comunicación
     * oficial de la TSS antes de usar esto en producción.
     */
    public function run(): void
    {
        $parametros = [
            [
                'clave'         => 'tasa_afp_empleado',
                'valor'         => 0.0287,
                'vigente_desde' => '2026-02-01',
                'fuente'        => 'Ley 87-01 / TSS',
            ],
            [
                'clave'         => 'tasa_sfs_empleado',
                'valor'         => 0.0304,
                'vigente_desde' => '2026-02-01',
                'fuente'        => 'Ley 87-01 / TSS',
            ],
            [
                'clave'         => 'monto_dependiente_adicional',
                'valor'         => 1919.78,
                'vigente_desde' => '2025-12-01',
                'fuente'        => 'Resolución 624-02 CNSS (incluye RD$32.24 Fonamat)',
            ],
            [
                'clave'         => 'tope_cotizacion_sfs',
                'valor'         => 232230.00,
                'vigente_desde' => '2026-02-01',
                'fuente'        => 'TSS — Resolución 01-2025, tss.gob.do/tss-informa-nuevos-topes-de-cotizacion-del-regimen-contributivo-del-sdss',
            ],
            [
                'clave'         => 'tope_cotizacion_afp',
                'valor'         => 464460.00,
                'vigente_desde' => '2026-02-01',
                'fuente'        => 'TSS — Resolución 01-2025, tss.gob.do/tss-informa-nuevos-topes-de-cotizacion-del-regimen-contributivo-del-sdss',
            ],
        ];

        foreach ($parametros as $parametro) {
            ParametroNomina::firstOrCreate(
                [
                    'clave'         => $parametro['clave'],
                    'vigente_desde' => $parametro['vigente_desde'],
                ],
                $parametro
            );
        }
    }
}
