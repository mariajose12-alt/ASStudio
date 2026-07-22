@php use App\Support\Dinero; @endphp
    <!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="{{ public_path('css/export-pdf.css') }}">
</head>
<body>

<h1>Nómina — {{ $nomina->periodo }}</h1>

<div class="subtitulo">
    Período: {{ $nomina->fecha_inicio->format('d/m/Y') }} —
    {{ $nomina->fecha_fin->format('d/m/Y') }} ·
    Estado:
    <span class="badge badge-{{ strtolower($nomina->estado) }}">
        {{ $nomina->estado }}
    </span>
    ·
    Creada por:
    {{ $nomina->creadaPor->empleado->usuario->persona->nombre ?? '—' }}
    {{ $nomina->creadaPor->empleado->usuario->persona->apellido ?? '' }}
</div>

{{-- Resumen General --}}
<table>
    <tr>
        <th colspan="2">Resumen General</th>
    </tr>
    <tr>
        <td>Total Salarios Brutos</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->total_salarios_brutos) }}
        </td>
    </tr>
    @if($nomina->total_regalia_pascual > 0)
        <tr>
            <td>Regalía Pascual</td>
            <td class="text-right">
                {{ Dinero::formato($nomina->total_regalia_pascual) }}
            </td>
        </tr>
    @endif
    @if($nomina->total_incentivos > 0)
        <tr>
            <td>Incentivos por Ventas</td>
            <td class="text-right">
                {{ Dinero::formato($nomina->total_incentivos) }}
            </td>
        </tr>
    @endif
    <tr>
        <td>TSS (SFS + AFP empleado)</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->detalles->sum('descuento_tss')) }}
        </td>
    </tr>
    <tr>
        <td>Dependientes adicionales (TSS)</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->detalles->sum('descuento_dependientes')) }}
        </td>
    </tr>
    <tr>
        <td>ISR retenido</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->total_isr_retenido) }}
        </td>
    </tr>
    <tr>
        <td>Total retenido a empleados</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->total_descuentos_legales) }}
        </td>
    </tr>
    <tr>
        <td>Aportes patronales (no descontado)</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->total_aportes_patronales) }}
        </td>
    </tr>
    <tr class="total-row">
        <td>Total Neto a Pagar</td>
        <td class="text-right">
            {{ Dinero::formato($nomina->total_nomina_neta) }}
        </td>
    </tr>
</table>

{{-- Desglose por fotógrafo --}}
@foreach($desglose as $detalle)
    @php
        $persona = $detalle->fotografo->empleado->usuario->persona;
    @endphp

    <div class="fotografo-header">
        {{ $persona->nombre }} {{ $persona->apellido }}
    </div>

    <div class="fotografo-subheader">
        Neto a recibir:
        <strong>{{ Dinero::formato($detalle->sueldo_neto) }}</strong>
    </div>

    <table>
        <tr>
            <th>Fecha</th>
            <th>Rol</th>
            <th class="text-right">Precio Reserva</th>
            <th class="text-right">% Comisión</th>
            <th class="text-right">Monto Generado</th>
        </tr>

        @forelse($detalle->participaciones_detalle as $participacion)
            <tr>
                <td>{{ $participacion->sesion->fecha_finalizacion->format('d/m/Y') }}</td>
                <td>{{ $participacion->rol }}</td>
                <td class="text-right">
                    {{ Dinero::formato($participacion->sesion->reserva->precio_total) }}
                </td>
                <td class="text-right">
                    {{ \App\Services\NominaService::tasaComision($participacion) }}%
                </td>
                <td class="text-right">
                    {{ Dinero::formato(\App\Services\NominaService::montoComision($participacion)) }}
                </td>
            </tr>
        @empty
            <tr>
                <td colspan="5" class="text-muted">
                    Sin sesiones registradas en este período.
                </td>
            </tr>
        @endforelse
    </table>

    <table class="resumen-fotografo">
        <tr>
            <td>Salario bruto</td>
            <td class="text-right">
                {{ Dinero::formato($detalle->salario_bruto) }}
            </td>
        </tr>
        <tr>
            <td>TSS (SFS + AFP)</td>
            <td class="text-right">
                {{ Dinero::formato($detalle->descuento_tss) }}
            </td>
        </tr>
        @if($detalle->dependientes_adicionales_aplicados > 0)
            <tr>
                <td>Dependientes adicionales </td>
                <td class="text-right">
                    {{ Dinero::formato($detalle->descuento_dependientes) }}
                </td>
            </tr>
        @endif
        <tr>
            <td>ISR retenido</td>
            <td class="text-right">
                {{ Dinero::formato($detalle->descuento_isr) }}
            </td>
        </tr>
        <tr>
            <td>Descuentos legales (total)</td>
            <td class="text-right">
                {{ Dinero::formato($detalle->descuentos_legales) }}
            </td>
        </tr>
        @if($detalle->regalia_pascual > 0)
            <tr>
                <td>Regalía Pascual</td>
                <td class="text-right">
                    {{ Dinero::formato($detalle->regalia_pascual) }}
                </td>
            </tr>
        @endif
        @if($detalle->incentivo_ventas > 0)
            <tr>
                <td>Incentivo por Ventas</td>
                <td class="text-right">
                    {{ Dinero::formato($detalle->incentivo_ventas) }}
                </td>
            </tr>
        @endif
        <tr>
            <td><strong>Neto</strong></td>
            <td class="text-right">
                <strong>{{ Dinero::formato($detalle->sueldo_neto) }}</strong>
            </td>
        </tr>
    </table>

@endforeach

</body>
</html>
