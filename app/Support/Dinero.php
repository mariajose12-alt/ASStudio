<?php

namespace App\Support;

use App\Models\ConfiguracionNomina;
use App\Models\ParametroNomina;

class Dinero
{
    /**
     * Formatea un monto para MOSTRARLO en pantalla. El cálculo interno de
     * NominaService SIEMPRE trabaja en pesos (RD$) — esta clase es solo capa
     * de presentación y nunca debe usarse dentro de la lógica de cálculo,
     * para no contaminar el bruto/neto real guardado en DetalleNomina.
     */
    public static function formato(float $montoEnPesos): string
    {
        $configuracion = ConfiguracionNomina::actual();

        if ($configuracion->moneda_display === 'USD') {
            $tasa = ParametroNomina::valorVigente('tasa_cambio_usd');
            $montoEnUsd = $tasa > 0 ? $montoEnPesos / $tasa : 0.0;

            return 'US$ ' . number_format($montoEnUsd, 2);
        }

        return 'RD$ ' . number_format($montoEnPesos, 2);
    }
}
