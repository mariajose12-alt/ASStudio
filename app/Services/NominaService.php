<?php

namespace App\Services;

use App\DTOs\NominaCalculoDTO;
use App\Mail\NominaDisponibleFotografo;
use App\Models\DetalleNomina;
use App\Models\Nomina;
use App\Models\ParticipacionSesion;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Mail;

class NominaService
{
    // Tasas RD vigentes — descuentos al empleado
    const TASA_SFS_EMPLEADO   = 0.0304; // 3.04 %
    const TASA_AFP_EMPLEADO   = 0.0287; // 2.87 %

    // Aportes patronales
    const TASA_SFS_PATRONAL   = 0.0709; // 7.09 %
    const TASA_AFP_PATRONAL   = 0.0710; // 7.10 %
    const TASA_RIESGO_LABORAL = 0.0120; // 1.20 %

    // Comisiones por defecto según rol (fallback si porcentaje_comision = 0)
    const COMISION_PRINCIPAL = 40.00;
    const COMISION_ASISTENTE = 20.00;

    // ISR — escala DGII 2026
    const ISR_EXENCION_ANUAL = 416220.00;
    const ISR_TRAMO_2_LIMITE = 624329.00;
    const ISR_TRAMO_3_LIMITE = 867123.00;
    const ISR_CUOTA_TRAMO_2  = 31216.00;
    const ISR_CUOTA_TRAMO_3  = 79776.00;

    public function calcular(NominaCalculoDTO $dto): Nomina
    {
        $nomina = Nomina::create([
            'creada_por_id' => $dto->creadaPorId,
            'periodo'       => $dto->periodo,
            'fecha_inicio'  => $dto->fechaInicio->toDateString(),
            'fecha_fin'     => $dto->fechaFin->toDateString(),
            'estado'        => 'PENDIENTE',
        ]);

        // Una sesión pertenece al período en el que pasó a FINALIZADA, usando updated_at
        $participaciones = ParticipacionSesion::whereHas('sesion', function ($q) use ($dto) {
            $q->where('estado', 'FINALIZADA')
                ->whereYear('updated_at', $dto->fechaInicio->year)
                ->whereMonth('updated_at', $dto->fechaInicio->month);
        })
            ->where('estado_participacion', true)
            ->with(['sesion.reserva', 'fotografo'])
            ->get();

        $porFotografo = $participaciones->groupBy('fotografo_id');

        $totBruto      = 0.0;
        $totDescuento  = 0.0;
        $totIsr        = 0.0;
        $totPatronal   = 0.0;
        $totNeto       = 0.0;

        foreach ($porFotografo as $fotografoId => $grupo) {
            // Sumar el salario base al bruto
            $fotografo    = $grupo->first()->fotografo;
            $salario_base = $fotografo->salarioBaseEfectivo();

            $bonoSesiones = $grupo->sum(function (ParticipacionSesion $participacion) {
                return self::montoComision($participacion);
            });

            $bruto       = round($salario_base + $bonoSesiones, 2);
            $tss         = $this->descuentoTSS($bruto);
            $isr         = $this->calcularISR($bruto);
            $descuento   = round($tss + $isr, 2);
            $patronal    = $this->aportesPatronales($bruto);
            $neto        = round($bruto - $descuento, 2);

            $detalle = DetalleNomina::create([
                'nomina_id'          => $nomina->id,
                'fotografo_id'       => $fotografoId,
                'salario_bruto'      => $bruto,
                'descuentos_legales' => $descuento,
                'descuento_tss'      => $tss,
                'descuento_isr'      => $isr,
                'sueldo_neto'        => $neto,
            ]);

            $emailFotografo = $detalle->fotografo->empleado->usuario->email;
            Mail::to($emailFotografo)->send(new NominaDisponibleFotografo($detalle));

            $totBruto     += $bruto;
            $totDescuento += $descuento;
            $totIsr       += $isr;
            $totPatronal  += $patronal;
            $totNeto      += $neto;
        }

        $nomina->update([
            'total_salarios_brutos'    => round($totBruto, 2),
            'total_descuentos_legales' => round($totDescuento, 2),
            'total_isr_retenido'       => round($totIsr, 2),
            'total_aportes_patronales' => round($totPatronal, 2),
            'total_nomina_neta'        => round($totNeto, 2),
            'estado'                   => 'CALCULADA',
        ]);

        return $nomina->load('detalles.fotografo.empleado.usuario.persona');
    }

    public static function tasaComision(ParticipacionSesion $participacion): float
    {
        return (float) $participacion->porcentaje_comision > 0
            ? (float) $participacion->porcentaje_comision
            : ($participacion->rol === 'PRINCIPAL' ? self::COMISION_PRINCIPAL : self::COMISION_ASISTENTE);
    }

    public static function montoComision(ParticipacionSesion $participacion): float
    {
        $precio = (float) $participacion->sesion->reserva->precio_total;
        return round($precio * (self::tasaComision($participacion) / 100), 2);
    }

    public function recalcularDetalle(DetalleNomina $detalle): DetalleNomina
    {
        $nomina    = $detalle->nomina;
        $fotografo = $detalle->fotografo;

        $participaciones = ParticipacionSesion::where('fotografo_id', $fotografo->id)
            ->whereHas('sesion', function ($q) use ($nomina) {
                $q->where('estado', 'FINALIZADA')
                    ->whereYear('updated_at', $nomina->fecha_inicio->year)
                    ->whereMonth('updated_at', $nomina->fecha_inicio->month);
            })
            ->where('estado_participacion', true)
            ->with('sesion.reserva')
            ->get();

        $salarioBase  = $fotografo->salarioBaseEfectivo();
        $bonoSesiones = $participaciones->sum(fn($p) => self::montoComision($p));

        $bruto     = round($salarioBase + $bonoSesiones, 2);
        $tss       = $this->descuentoTSS($bruto);
        $isr       = $this->calcularISR($bruto);
        $descuento = round($tss + $isr, 2);
        $neto      = round($bruto - $descuento, 2);

        $detalle->update([
            'salario_bruto'      => $bruto,
            'descuentos_legales' => $descuento,
            'descuento_tss'      => $tss,
            'descuento_isr'      => $isr,
            'sueldo_neto'        => $neto,
        ]);

        // Recalcular los totales de la Nomina padre también
        $this->recalcularTotalesNomina($nomina);

        return $detalle;
    }

    private function recalcularTotalesNomina(Nomina $nomina): void
    {
        $nomina->load('detalles');

        $nomina->update([
            'total_salarios_brutos'    => round($nomina->detalles->sum('salario_bruto'), 2),
            'total_descuentos_legales' => round($nomina->detalles->sum('descuentos_legales'), 2),
            'total_isr_retenido'       => round($nomina->detalles->sum('descuento_isr'), 2),
            'total_nomina_neta'        => round($nomina->detalles->sum('sueldo_neto'), 2),
        ]);
    }

    public function detallesPorFotografo(Nomina $nomina): Collection
    {
        return $nomina->detalles()->with('fotografo.empleado.usuario.persona')->get();
    }

    /**
     * Descuento de Tesorería de la Seguridad Social (SFS + AFP) al empleado.
     */
    private function descuentoTSS(float $bruto): float
    {
        return round($bruto * (self::TASA_SFS_EMPLEADO + self::TASA_AFP_EMPLEADO), 2);
    }

    /**
     * ISR retenido al empleado (agente de retención: el estudio, a favor de la DGII).
     * Se calcula sobre el neto gravable (bruto - TSS), anualizado, según la
     * escala progresiva vigente (Resolución DDG-AR1-2026-00001).
     */
    private function calcularISR(float $bruto): float
    {
        $netoGravable = $bruto - $this->descuentoTSS($bruto);
        $anualizado   = $netoGravable * 12;

        $isrAnual = match (true) {
            $anualizado <= self::ISR_EXENCION_ANUAL => 0,
            $anualizado <= self::ISR_TRAMO_2_LIMITE  => ($anualizado - self::ISR_EXENCION_ANUAL) * 0.15,
            $anualizado <= self::ISR_TRAMO_3_LIMITE  => self::ISR_CUOTA_TRAMO_2 + ($anualizado - self::ISR_TRAMO_2_LIMITE) * 0.20,
            default                                   => self::ISR_CUOTA_TRAMO_3 + ($anualizado - self::ISR_TRAMO_3_LIMITE) * 0.25,
        };

        return round($isrAnual / 12, 2);
    }

    /**
     * Aportes patronales (SFS + AFP + Riesgo Laboral) que paga el estudio
     * directamente — no se descuenta al fotógrafo.
     */
    private function aportesPatronales(float $bruto): float
    {
        return round($bruto * (self::TASA_SFS_PATRONAL + self::TASA_AFP_PATRONAL + self::TASA_RIESGO_LABORAL), 2);
    }
}
