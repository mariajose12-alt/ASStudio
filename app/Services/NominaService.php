<?php

namespace App\Services;

use App\DTOs\NominaCalculoDTO;
use App\Mail\NominaDisponibleFotografo;
use App\Models\DetalleNomina;
use App\Models\Nomina;
use App\Models\ParametroNomina;
use App\Models\ParticipacionSesion;
use App\Models\TramoIsr;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
//use App\Notifications\NominaDisponibleFotografo;
use Illuminate\Support\Facades\Mail;
use App\Models\ConfiguracionNomina;
use App\Models\Fotografo;

class NominaService
{
    // Tasas TSS empleado (AFP, SFS), tasas patronales, monto por dependiente
    // adicional y topes de cotización ya NO están hardcodeados: se leen de
    // ParametroNomina, versionados por fecha, para poder ajustarlos sin
    // deploy y para poder recalcular nóminas de meses pasados con la tarifa
    // que estaba vigente en ese momento.

    // Comisiones por defecto según rol (fallback si porcentaje_comision = 0)
    const COMISION_PRINCIPAL = 40.00;
    const COMISION_ASISTENTE = 20.00;

    public function calcular(NominaCalculoDTO $dto): Nomina
    {
        $existente = Nomina::where('periodo', $dto->periodo)->first();

        if ($existente && !$existente->puedeModificarse()) {
            // El controller ya bloquea esto antes de llegar aquí, pero el
            // service se protege también por si se llama desde otro lugar.
            throw new \RuntimeException("La nómina de {$dto->periodo} ya está {$existente->estado} y no puede recalcularse.");
        }

        if ($existente) {
            // Recalculo de una nómina aún no oficial (PENDIENTE/CALCULADA):
            // se limpian sus detalles viejos y se reutiliza la misma fila,
            // en vez de crear una nueva y violar el unique de periodo.
            $existente->detalles()->delete();

            $nomina = tap($existente)->update([
                'creada_por_id' => $dto->creadaPorId,
                'fecha_inicio'  => $dto->fechaInicio->toDateString(),
                'fecha_fin'     => $dto->fechaFin->toDateString(),
                'estado'        => 'PENDIENTE',
            ]);
        } else {
            $nomina = Nomina::create([
                'creada_por_id' => $dto->creadaPorId,
                'periodo'       => $dto->periodo,
                'fecha_inicio'  => $dto->fechaInicio->toDateString(),
                'fecha_fin'     => $dto->fechaFin->toDateString(),
                'estado'        => 'PENDIENTE',
            ]);
        }

        // Ya NO se agrupa por "quién tuvo participaciones" — eso dejaba
        // fuera a cualquier fotógrafo activo sin sesiones ese mes, y un
        // fotógrafo activo siempre debe cobrar al menos su salario base.
        // En vez de eso: se parte de TODOS los fotógrafos activos, y para
        // cada uno se buscan sus participaciones del período (que pueden
        // ser una colección vacía sin ningún problema).
        $participaciones = ParticipacionSesion::whereHas('sesion', function ($q) use ($dto) {
            $q->where('estado', 'FINALIZADA')
                ->whereYear('fecha_finalizacion', $dto->fechaInicio->year)
                ->whereMonth('fecha_finalizacion', $dto->fechaInicio->month);
        })
            ->where('estado_participacion', true)
            ->with(['sesion.reserva'])
            ->get();

        $participacionesPorFotografo = $participaciones->groupBy('fotografo_id');

        $fotografosActivos = Fotografo::whereHas('empleado.usuario', function ($q) {
            $q->where('estado', 'ACTIVO');
        })->with('empleado.usuario.persona')->get();

        // Se congela el toggle de incentivos vigente AHORA, al momento de
        // calcular. Si el admin lo activa/desactiva después, esta nómina ya
        // calculada no se ve afectada retroactivamente (mismo criterio que
        // ya se usa con $fechaVigencia para las tarifas de TSS/ISR).
        $incentivosActivos = ConfiguracionNomina::actual()->incentivos_activos;

        $totBruto      = 0.0;
        $totDescuento  = 0.0;
        $totIsr        = 0.0;
        $totPatronal   = 0.0;
        $totNeto       = 0.0;
        $totRegalia    = 0.0;
        $totIncentivos = 0.0;

        $fechaVigencia = $dto->fechaInicio;

        foreach ($fotografosActivos as $fotografo) {
            $fotografoId  = $fotografo->id;
            $grupo        = $participacionesPorFotografo->get($fotografoId, collect());
            $salario_base = $fotografo->salarioBaseEfectivo();
            $dependientes = $fotografo->dependientes_adicionales ?? 0;

            $bonoSesiones = $grupo->sum(function (ParticipacionSesion $participacion) {
                return self::montoComision($participacion);
            });

            // El "tope de ventas" se mide sobre lo que el fotógrafo generó en
            // comisiones ese mes (bonoSesiones), no sobre el bruto total.
            $incentivo = $incentivosActivos
                ? $this->calcularIncentivo($bonoSesiones, $fechaVigencia)
                : 0.0;

            $bruto     = round($salario_base + $bonoSesiones + $incentivo, 2);
            $tss       = $this->descuentoTSS($bruto, $dependientes, $fechaVigencia);
            $isr       = $this->calcularISR($bruto, $dependientes, $fechaVigencia);
            $descuento = round($tss['afp'] + $tss['sfs'] + $tss['dependientes'] + $isr, 2);
            $patronal  = $this->aportesPatronales($bruto, $fechaVigencia);
            $neto      = round($bruto - $descuento, 2);

            // Diciembre: se suma la Regalía Pascual, sin descuentos, aparte del neto ordinario.
            $regalia = $dto->fechaInicio->month === 12
                ? $this->regaliaPascual($fotografoId, $dto->fechaInicio->year, $bruto)
                : 0.0;

            $netoConRegalia = round($neto + $regalia, 2);

            $detalle = DetalleNomina::create([
                'nomina_id'          => $nomina->id,
                'fotografo_id'       => $fotografoId,
                'salario_bruto'      => $bruto,
                'descuentos_legales' => $descuento,
                'descuento_tss'      => round($tss['afp'] + $tss['sfs'], 2),
                'descuento_isr'      => $isr,
                'dependientes_adicionales_aplicados' => $dependientes,
                'monto_dependiente_unitario_usado'   => $tss['monto_dependiente_unitario'],
                'descuento_dependientes'             => $tss['dependientes'],
                'regalia_pascual'    => $regalia,
                'incentivo_ventas'   => $incentivo,
                'sueldo_neto'        => $netoConRegalia,
            ]);

            $usuarioFotografo = $detalle->fotografo->empleado->usuario;
            if ($usuarioFotografo) {
                $usuarioFotografo->notify(new NominaDisponibleFotografo($detalle));
            }

            $totBruto      += $bruto;
            $totDescuento  += $descuento;
            $totIsr        += $isr;
            $totPatronal   += $patronal;
            $totNeto       += $netoConRegalia;
            $totRegalia    += $regalia;
            $totIncentivos += $incentivo;
        }

        $nomina->update([
            'total_salarios_brutos'    => round($totBruto, 2),
            'total_descuentos_legales' => round($totDescuento, 2),
            'total_isr_retenido'       => round($totIsr, 2),
            'total_aportes_patronales' => round($totPatronal, 2),
            'total_nomina_neta'        => round($totNeto, 2),
            'total_regalia_pascual'    => round($totRegalia, 2),
            'total_incentivos'         => round($totIncentivos, 2),
            'incentivos_activos'       => $incentivosActivos,
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
                    ->whereYear('fecha_finalizacion', $nomina->fecha_inicio->year)
                    ->whereMonth('fecha_finalizacion', $nomina->fecha_inicio->month);
            })
            ->where('estado_participacion', true)
            ->with('sesion.reserva')
            ->get();

        $salarioBase  = $fotografo->salarioBaseEfectivo();
        $dependientes = $fotografo->dependientes_adicionales ?? 0;
        $bonoSesiones = $participaciones->sum(fn($p) => self::montoComision($p));

        // Recalcular con la tarifa vigente en el período original de la nómina,
        // no con la de hoy, para no alterar retroactivamente un recibo ya emitido.
        $fechaVigencia = $nomina->fecha_inicio;

        // Importante: usar el toggle YA CONGELADO en esta Nomina
        // ($nomina->incentivos_activos), no ConfiguracionNomina::actual().
        // Si se usara el valor actual, un cambio de toggle del admin después
        // de calcular podría activar/desactivar el incentivo retroactivamente
        // en un ajuste de disputa, lo cual sería inconsistente con el resto
        // de fotógrafos de la misma nómina que no se recalculan.
        $incentivo = $nomina->incentivos_activos
            ? $this->calcularIncentivo($bonoSesiones, $fechaVigencia)
            : 0.0;


        $bruto     = round($salarioBase + $bonoSesiones + $incentivo, 2);
        $tss       = $this->descuentoTSS($bruto, $dependientes, $fechaVigencia);
        $isr       = $this->calcularISR($bruto, $dependientes, $fechaVigencia);
        $descuento = round($tss['afp'] + $tss['sfs'] + $tss['dependientes'] + $isr, 2);
        $neto      = round($bruto - $descuento, 2);

        // Diciembre: recalcular también la Regalía Pascual, excluyendo el
        // propio detalle viejo de la suma (se recalcula con el bruto nuevo).
        $regalia = $fechaVigencia->month === 12
            ? $this->regaliaPascual($fotografo->id, $fechaVigencia->year, $bruto, $nomina->id)
            : 0.0;

        $netoConRegalia = round($neto + $regalia, 2);

        $detalle->update([
            'salario_bruto'      => $bruto,
            'descuentos_legales' => $descuento,
            'descuento_tss'      => round($tss['afp'] + $tss['sfs'], 2),
            'descuento_isr'      => $isr,
            'dependientes_adicionales_aplicados' => $dependientes,
            'monto_dependiente_unitario_usado'   => $tss['monto_dependiente_unitario'],
            'descuento_dependientes'             => $tss['dependientes'],
            'regalia_pascual'    => $regalia,
            'incentivo_ventas'   => $incentivo,
            'sueldo_neto'        => $netoConRegalia,
        ]);

        // Recalcular los totales de la Nomina padre también
        $this->recalcularTotalesNomina($nomina);

        return $detalle;
    }

    private function recalcularTotalesNomina(Nomina $nomina): void
    {
        $nomina->load('detalles');

        // Los aportes patronales no se guardan por detalle (solo el total a
        // nivel de Nomina), así que hay que recalcularlos sumando cada
        // fotógrafo con la tarifa vigente en el período original
        $totPatronal = $nomina->detalles->sum(
            fn (DetalleNomina $detalle) => $this->aportesPatronales($detalle->salario_bruto, $nomina->fecha_inicio)
        );

        $nomina->update([
            'total_salarios_brutos'    => round($nomina->detalles->sum('salario_bruto'), 2),
            'total_descuentos_legales' => round($nomina->detalles->sum('descuentos_legales'), 2),
            'total_isr_retenido'       => round($nomina->detalles->sum('descuento_isr'), 2),
            'total_aportes_patronales' => round($totPatronal, 2),
            'total_nomina_neta'        => round($nomina->detalles->sum('sueldo_neto'), 2),
            'total_regalia_pascual'    => round($nomina->detalles->sum('regalia_pascual'), 2),
            'total_incentivos'         => round($nomina->detalles->sum('incentivo_ventas'), 2),
        ]);
    }

    public function detallesPorFotografo(Nomina $nomina): Collection
    {
        return $nomina->detalles()->with('fotografo.empleado.usuario.persona')->get();
    }

    /**
     * Descuento de Tesorería de la Seguridad Social (SFS + AFP + dependientes
     * adicionales) al empleado. Devuelve el desglose completo porque se
     * necesita guardar cada componente por separado en DetalleNomina para
     * auditoría.
     *
     * $dependientesAdicionales son SOLO los registrados fuera del núcleo
     * familiar directo (cónyuge/hijos menores, que ya están cubiertos sin
     * costo extra por el 3.04% de SFS).
     *
     * @return array{afp: float, sfs: float, dependientes: float, monto_dependiente_unitario: float, total: float}
     */
    private function descuentoTSS(float $bruto, int $dependientesAdicionales, Carbon $fecha): array
    {
        $tasaAfp = ParametroNomina::valorVigente('tasa_afp_empleado', $fecha);
        $tasaSfs = ParametroNomina::valorVigente('tasa_sfs_empleado', $fecha);
        $montoDependiente = ParametroNomina::valorVigente('monto_dependiente_adicional', $fecha);

        $topeSfs = ParametroNomina::valorVigente('tope_cotizacion_sfs', $fecha);
        $topeAfp = ParametroNomina::valorVigente('tope_cotizacion_afp', $fecha);

        $baseSfs = min($bruto, $topeSfs);
        $baseAfp = min($bruto, $topeAfp);

        $afp = round($baseAfp * $tasaAfp, 2);
        $sfs = round($baseSfs * $tasaSfs, 2);
        $dependientes = round($dependientesAdicionales * $montoDependiente, 2);

        return [
            'afp'          => $afp,
            'sfs'          => $sfs,
            'dependientes' => $dependientes,
            'monto_dependiente_unitario' => $montoDependiente,
            'total'        => round($afp + $sfs + $dependientes, 2),
        ];
    }

    /**
     * ISR retenido al empleado (agente de retención: el estudio, a favor de la DGII).
     * Se calcula sobre el neto gravable (bruto - TSS incluyendo dependientes),
     * anualizado, según la escala progresiva vigente en TramoIsr.
     */
    private function calcularISR(float $bruto, int $dependientesAdicionales, Carbon $fecha): float
    {
        $tss = $this->descuentoTSS($bruto, $dependientesAdicionales, $fecha);
        $netoGravable = $bruto - $tss['total'];
        $anualizado   = $netoGravable * 12;

        $tramo = TramoIsr::paraMonto($anualizado, $fecha);

        $isrAnual = ($anualizado - (float) $tramo->desde_anual) * (float) $tramo->tasa + (float) $tramo->monto_fijo_adicional;

        return round($isrAnual / 12, 2);
    }

    /**
     * Aportes patronales (SFS + AFP + Riesgo Laboral) que paga el estudio
     * directamente — no se descuenta al fotógrafo. Cada componente tiene su
     * propio tope de cotización, así que se aplican por separado antes de sumar.
     */
    private function aportesPatronales(float $bruto, Carbon $fecha): float
    {
        $tasaSfs    = ParametroNomina::valorVigente('tasa_sfs_patronal', $fecha);
        $tasaAfp    = ParametroNomina::valorVigente('tasa_afp_patronal', $fecha);
        $tasaRiesgo = ParametroNomina::valorVigente('tasa_riesgo_laboral', $fecha);

        $topeSfs    = ParametroNomina::valorVigente('tope_cotizacion_sfs', $fecha);
        $topeAfp    = ParametroNomina::valorVigente('tope_cotizacion_afp', $fecha);
        $topeRiesgo = ParametroNomina::valorVigente('tope_cotizacion_riesgo_laboral', $fecha);

        $sfs    = round(min($bruto, $topeSfs) * $tasaSfs, 2);
        $afp    = round(min($bruto, $topeAfp) * $tasaAfp, 2);
        $riesgo = round(min($bruto, $topeRiesgo) * $tasaRiesgo, 2);

        return round($sfs + $afp + $riesgo, 2);
    }
    /**
     * Regalía Pascual (Art. 219 Código de Trabajo RD): 1/12 del salario
     * ordinario total devengado en el año (enero-diciembre), pagadero en
     * diciembre. Al sumar el salario_bruto real de cada DetalleNomina del
     * fotógrafo en el año, el prorrateo por meses no trabajados sale solo
     * — un fotógrafo que empezó en marzo simplemente no tiene detalles en
     * enero/febrero, que aportan 0 a la suma.
     *
     * No lleva descuentos de TSS ni ISR: la ley la trata como un pago
     * especial, no como salario ordinario del mes.
     *
     * $excluirNominaId excluye una nómina puntual de la suma (usado en
     * recalcularDetalle, para no arrastrar el propio registro de diciembre
     * si en algún momento se recalculara sobre sí mismo).
     */
    private function regaliaPascual(int $fotografoId, int $anio, float $brutoDiciembre, ?int $excluirNominaId = null): float
    {
        $brutoEneroANoviembre = DetalleNomina::where('fotografo_id', $fotografoId)
            ->when($excluirNominaId, fn($q) => $q->where('nomina_id', '!=', $excluirNominaId))
            ->whereHas('nomina', function ($q) use ($anio) {
                $q->whereYear('fecha_inicio', $anio)
                    ->whereMonth('fecha_inicio', '<', 12);
            })
            ->sum('salario_bruto');

        $brutoAnual = (float) $brutoEneroANoviembre + $brutoDiciembre;

        return round($brutoAnual / 12, 2);
    }

    /**
     * Incentivo por ventas: si el fotógrafo generó en comisiones ese mes más
     * que el tope configurado por el admin, se le paga un % fijo sobre el
     * excedente. Se trata como bruto normal (lleva TSS/ISR), a diferencia de
     * la regalía, así que se suma ANTES del cálculo de descuentos, no después.
     */
    private function calcularIncentivo(float $ventasGeneradas, Carbon $fecha): float
    {
        $tope       = ParametroNomina::valorVigente('tope_ventas_incentivo', $fecha);
        $porcentaje = ParametroNomina::valorVigente('porcentaje_incentivo', $fecha);

        if ($ventasGeneradas <= $tope) {
            return 0.0;
        }

        return round(($ventasGeneradas - $tope) * ($porcentaje / 100), 2);
    }
}
