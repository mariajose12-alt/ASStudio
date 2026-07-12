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
            RD$ {{ number_format($nomina->total_salarios_brutos, 2) }}
        </td>
    </tr>
    <tr>
        <td>TSS (SFS + AFP empleado)</td>
        <td class="text-right">
            RD$ {{ number_format($nomina->total_descuentos_legales - $nomina->total_isr_retenido, 2) }}
        </td>
    </tr>
    <tr>
        <td>ISR retenido</td>
        <td class="text-right">
            RD$ {{ number_format($nomina->total_isr_retenido, 2) }}
        </td>
    </tr>
    <tr>
        <td>Total retenido a empleados</td>
        <td class="text-right">
            RD$ {{ number_format($nomina->total_descuentos_legales, 2) }}
        </td>
    </tr>
    <tr>
        <td>Aportes patronales (no descontado)</td>
        <td class="text-right">
            RD$ {{ number_format($nomina->total_aportes_patronales, 2) }}
        </td>
    </tr>
    <tr class="total-row">
        <td>Total Neto a Pagar</td>
        <td class="text-right">
            RD$ {{ number_format($nomina->total_nomina_neta, 2) }}
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
        <strong>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</strong>
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
                <td>{{ $participacion->sesion->updated_at->format('d/m/Y') }}</td>
                <td>{{ $participacion->rol }}</td>
                <td class="text-right">
                    RD$ {{ number_format($participacion->sesion->reserva->precio_total, 2) }}
                </td>
                <td class="text-right">
                    {{ $participacion->porcentaje_comision ?? ($participacion->rol === 'PRINCIPAL' ? 40 : 20) }}%
                </td>
                <td class="text-right">
                    RD$ {{ number_format($participacion->monto_generado ?? 0, 2) }}
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
            <td>Salario base</td>
            <td class="text-right">
                RD$ {{ number_format($detalle->salario_bruto, 2) }}
            </td>
        </tr>
        <tr>
            <td>Descuentos legales</td>
            <td class="text-right">
                RD$ {{ number_format($detalle->descuentos_legales, 2) }}
            </td>
        </tr>
        <tr>
            <td><strong>Neto</strong></td>
            <td class="text-right">
                <strong>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</strong>
            </td>
        </tr>
    </table>

@endforeach

</body>
</html>
