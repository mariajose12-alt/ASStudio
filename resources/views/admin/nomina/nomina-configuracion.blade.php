@extends('layouts.admin')
@section('title', 'Configuración de Nómina')

@section('topbar-actions')
    <a href="{{ route('admin.nomina') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')

    @if(session('success'))
        <div class="alert-success">{{ session('success') }}</div>
    @endif

    @php
        $etiquetas = [
            'tasa_afp_empleado'              => 'Tasa AFP (empleado) %',
            'tasa_sfs_empleado'              => 'Tasa SFS (empleado) %',
            'tasa_afp_patronal'              => 'Tasa AFP (patronal) %',
            'tasa_sfs_patronal'              => 'Tasa SFS (patronal) %',
            'tasa_riesgo_laboral'            => 'Tasa Riesgo Laboral %',
            'tope_cotizacion_afp'            => 'Tope de cotización AFP (RD$)',
            'tope_cotizacion_sfs'            => 'Tope de cotización SFS (RD$)',
            'tope_cotizacion_riesgo_laboral' => 'Tope de cotización Riesgo Laboral (RD$)',
            'monto_dependiente_adicional'    => 'Monto por dependiente adicional (RD$)',
        ];
    @endphp

    {{-- Sección 1: Parámetros legales (TSS/dependientes) --}}
    <div class="card">
        <div class="card-header">
            <h2>Parámetros Legales</h2>
            <span class="config-nota">Cambios aplican a partir de hoy — no afectan nóminas ya calculadas</span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.nomina.configuracion.parametros') }}">
                @csrf
                <div class="config-grid">
                    @foreach($etiquetas as $clave => $etiqueta)
                        <div class="config-field">
                            <label for="valor-{{ $clave }}">{{ $etiqueta }}</label>
                            <input
                                type="number"
                                step="0.0001"
                                min="0"
                                id="valor-{{ $clave }}"
                                name="valores[{{ $clave }}]"
                                value="{{ old('valores.' . $clave, $parametrosLegales[$clave]) }}"
                            >
                        </div>
                    @endforeach
                </div>
                <div class="config-acciones">
                    <button type="submit" class="btn btn-primary">Guardar parámetros legales</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sección 2: Incentivos por ventas --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h2>Incentivos por Ventas</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.nomina.configuracion.incentivos') }}">
                @csrf
                <div class="config-toggle-row">
                    <input type="hidden" name="incentivos_activos" value="0">
                    <label class="config-toggle">
                        <input
                            type="checkbox"
                            name="incentivos_activos"
                            value="1"
                            {{ $configuracion->incentivos_activos ? 'checked' : '' }}
                        >
                        <span>Activar incentivos por ventas</span>
                    </label>
                </div>

                <div class="config-grid">
                    <div class="config-field">
                        <label for="tope_ventas_incentivo">Tope de ventas mensual (RD$)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            id="tope_ventas_incentivo"
                            name="tope_ventas_incentivo"
                            value="{{ old('tope_ventas_incentivo', $topeVentasIncentivo) }}"
                        >
                    </div>
                    <div class="config-field">
                        <label for="porcentaje_incentivo">Porcentaje de incentivo sobre el excedente (%)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0"
                            max="100"
                            id="porcentaje_incentivo"
                            name="porcentaje_incentivo"
                            value="{{ old('porcentaje_incentivo', $porcentajeIncentivo) }}"
                        >
                    </div>
                </div>

                <p class="config-nota">
                    Si un fotógrafo genera más que el tope en comisiones ese mes, se le paga este % sobre el excedente
                    (lleva TSS e ISR, igual que su bruto normal).
                </p>

                <div class="config-acciones">
                    <button type="submit" class="btn btn-primary">Guardar configuración de incentivos</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Sección 3: Moneda --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h2>Moneda</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.nomina.configuracion.moneda') }}">
                @csrf
                <div class="config-grid">
                    <div class="config-field">
                        <label for="moneda_display">Moneda de visualización</label>
                        <select id="moneda_display" name="moneda_display">
                            <option value="RD$" {{ $configuracion->moneda_display === 'RD$' ? 'selected' : '' }}>Pesos dominicanos (RD$)</option>
                            <option value="USD" {{ $configuracion->moneda_display === 'USD' ? 'selected' : '' }}>Dólares (USD)</option>
                        </select>
                    </div>
                    <div class="config-field">
                        <label for="tasa_cambio_usd">Tasa de cambio (RD$ por USD)</label>
                        <input
                            type="number"
                            step="0.01"
                            min="0.01"
                            id="tasa_cambio_usd"
                            name="tasa_cambio_usd"
                            value="{{ old('tasa_cambio_usd', $tasaCambioUsd) }}"
                        >
                    </div>
                </div>

                <p class="config-nota">
                    El cálculo interno siempre se hace en pesos — esto solo cambia cómo se muestran los montos en pantalla.
                </p>

                <div class="config-acciones">
                    <button type="submit" class="btn btn-primary">Guardar configuración de moneda</button>
                </div>
            </form>
        </div>
    </div>

@endsection
