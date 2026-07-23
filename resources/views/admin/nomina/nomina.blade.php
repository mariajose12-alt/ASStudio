@extends('layouts.admin')
@section('title', 'Nómina')

@section('content')
    @if(session('nomina_calculada'))
        <div class="alert alert-success" style="margin-bottom:16px; padding:12px 16px; background:#d1fae5; color:#065f46; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
            <span>¡Nómina calculada correctamente!</span>
            <a href="{{ route('admin.nomina.resumen', session('nomina_calculada')) }}" class="btn btn-primary btn-sm">
                Ver resumen
            </a>
        </div>
    @endif

    @if(session('error'))
        <div class="alert" style="margin-bottom:16px; padding:12px 16px; background:#fee2e2; color:#991b1b; border-radius:8px;">
            ⚠️ {{ session('error') }}
        </div>
    @endif

    @if(session('periodo_ya_procesado'))
        <div class="alert" style="margin-bottom:16px; padding:12px 16px; background:#fef3c7; color:#92400e; border-radius:8px;">
            <p style="margin:0 0 10px;">
                ⚠️ El período <strong>{{ session('periodo_ya_procesado') }}</strong> ya fue procesado anteriormente.
                ¿Deseas recalcularlo? Esto generará una nueva nómina para el mismo período.
            </p>
            <form method="POST" action="{{ route('admin.nomina.calcular') }}">
                @csrf
                <input type="hidden" name="mes" value="{{ request('mes') }}">
                <input type="hidden" name="anio" value="{{ request('anio') }}">
                <input type="hidden" name="confirmar_recalculo" value="1">
                <button type="submit" class="btn btn-primary btn-sm">
                    Sí, recalcular
                </button>
            </form>
        </div>
    @endif

    <div class="reservas-wrapper">

        {{-- TABS --}}
        <div class="reservas-tabs">
            <button class="tab active" data-tab="calcular">Calcular Nómina</button>
            <button class="tab" data-tab="historial">Historial</button>
            <button class="tab" data-tab="disputas">Disputas</button>
            <button class="tab" data-tab="configuracion">Configuración</button>
        </div>

        {{-- PANEL: Calcular --}}
        <div class="tab-panel active" id="panel-calcular">
            <div class="card">
                <div class="card-header">
                    <h2>Selecciona el período</h2>
                    <span style="font-size:12px; color:var(--muted);">Elige mes y año para calcular la nómina</span>
                </div>

                <form method="GET" action="{{ route('admin.nomina') }}" style="padding:20px; display:flex; gap:16px; align-items:end; flex-wrap:wrap;">
                    <div>
                        <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Mes</label>
                        <select name="mes" onchange="this.form.submit()" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:160px;">
                            @foreach($meses as $num => $nombre)
                                <option value="{{ $num }}" {{ (int) request('mes', now()->month) === $num ? 'selected' : '' }}>
                                    {{ $nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Año</label>
                        <select name="anio" onchange="this.form.submit()" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:120px;">
                            @foreach($anios as $anio)
                                <option value="{{ $anio }}" {{ (int) request('anio', now()->year) === $anio ? 'selected' : '' }}>
                                    {{ $anio }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </form>
            </div>

            {{-- Listado de fotógrafos activos del período --}}
            <div class="card" style="margin-top:20px;">
                <div class="card-header">
                    <h2>Fotógrafos con sesiones en {{ $meses[(int) request('mes', now()->month)] }} {{ request('anio', now()->year) }}</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $fotografos->count() }} fotógrafo(s) encontrado(s)</span>
                </div>

                @if($fotografos->isEmpty())
                    <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                        No hay sesiones finalizadas en este período.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Fotógrafo</th>
                                <th>Sesiones como Principal</th>
                                <th>Sesiones como Asistente</th>
                                <th>Total sesiones</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($fotografos as $fotografo)
                                <tr>
                                    <td>
                                        {{ $fotografo->empleado->usuario->persona->nombre ?? '' }}
                                        {{ $fotografo->empleado->usuario->persona->apellido ?? '' }}
                                    </td>
                                    <td>{{ $fotografo->sesiones_principal }}</td>
                                    <td>{{ $fotografo->sesiones_asistente }}</td>
                                    <td><strong>{{ $fotografo->total_sesiones }}</strong></td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>

                    <div style="padding:16px 20px; text-align:right;">
                        <form method="POST" action="{{ route('admin.nomina.calcular') }}">
                            @csrf
                            <input type="hidden" name="mes" value="{{ request('mes', now()->month) }}">
                            <input type="hidden" name="anio" value="{{ request('anio', now()->year) }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Calcular Nómina
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL: Historial --}}
        <div class="tab-panel" id="panel-historial">
            <div class="card">
                <div class="card-header">
                    <h2>Historial de Nóminas Procesadas</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $nominas->count() }} registro(s)</span>
                </div>

                @if($nominas->isEmpty())
                    <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                        Aún no se ha calculado ninguna nómina.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Período</th>
                                <th>Calculada por</th>
                                <th>Fotógrafos</th>
                                <th>Total Neto</th>
                                <th>Estado</th>
                                <th>Fecha de cálculo</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nominas as $nomina)
                                <tr>
                                    <td>{{ $nomina->periodo }}</td>
                                    <td>
                                        {{ $nomina->creadaPor->empleado->usuario->persona->nombre ?? '—' }}
                                        {{ $nomina->creadaPor->empleado->usuario->persona->apellido ?? '' }}
                                    </td>
                                    <td>{{ $nomina->detalles->count() }}</td>
                                    <td>RD$ {{ number_format($nomina->total_nomina_neta, 2) }}</td>
                                    <td>
                                        @php
                                            $colores = [
                                                'PENDIENTE'  => 'badge-inactive',
                                                'CALCULADA'  => 'badge-foto',
                                                'CONFIRMADA' => 'badge-active',
                                                'PAGADA'     => 'badge-active',
                                                'CERRADA'    => 'badge-admin',
                                            ];
                                        @endphp
                                        <span class="badge {{ $colores[$nomina->estado] ?? 'badge-admin' }}">
                                                {{ $nomina->estado }}
                                            </span>
                                    </td>
                                    <td>{{ $nomina->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.nomina.resumen', $nomina) }}" class="btn btn-outline btn-sm">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL: Disputas --}}
        <div class="tab-panel" id="panel-disputas">
            <div class="card">
                <div class="card-header">
                    <h2>Disputas Pendientes de Resolución</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $disputas->count() }} reporte(s)</span>
                </div>

                @if($disputas->isEmpty())
                    <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                        No hay disputas pendientes.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Fotógrafo</th>
                                <th>Período</th>
                                <th>Observación</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($disputas as $disputa)
                                @php $persona = $disputa->fotografo->empleado->usuario->persona; @endphp
                                <tr>
                                    <td>{{ $persona->nombre }} {{ $persona->apellido }}</td>
                                    <td>{{ $disputa->nomina->periodo }}</td>
                                    <td style="max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $disputa->observacion_fotografo }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.nomina.disputa.show', $disputa) }}" class="btn btn-primary btn-sm">
                                            Resolver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL: Configuración --}}
        <div class="tab-panel" id="panel-configuracion">

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
                    <span class="config-nota">
                        Cambios aplican a partir de hoy — no afectan nóminas ya calculadas
                     </span>
                </div>

                <div class="card-body">
                    <form method="POST" action="{{ route('admin.nomina.configuracion.parametros') }}">
                        @csrf
                        <div class="table-wrap config-table-wrap">
                            <table class="table config-table">
                                <thead>
                                    <tr>
                                        <th style="width:55%">Parámetro</th>
                                        <th style="width:25%; text-align:center;">Valor</th>
                                        <th style="width:20%; text-align:center;">Unidad</th>
                                    </tr>
                                </thead>

                                <tbody>
                                    @foreach($etiquetas as $clave => $etiqueta)
                                        @php
                                            $unidad = str_contains(strtolower($etiqueta), '%')
                                                ? '%'
                                                : 'RD$';
                                        @endphp
                                        <tr>
                                            <td>
                                                {{ str_replace([' %',' (RD$)'], '', $etiqueta) }}
                                            </td>

                                            <td>
                                                <input
                                                    class="config-table-input"
                                                    type="number"
                                                    step="0.0001"
                                                    min="0"
                                                    name="valores[{{ $clave }}]"
                                                    value="{{ old('valores.' . $clave, $parametrosLegales[$clave]) }}"
                                                >
                                            </td>

                                            <td class="config-unit">
                                                {{ $unidad }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <div class="config-acciones">
                            <button type="submit" class="btn btn-primary">
                                Guardar parámetros legales
                            </button>
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
                        <input type="hidden" name="incentivos_activos" value="0">
                        <div class="config-toggle-row">
                            <div class="config-toggle">
                                <input
                                    id="incentivos_activos"
                                    type="checkbox"
                                    name="incentivos_activos"
                                    value="1"
                                    {{ $configuracion->incentivos_activos ? 'checked' : '' }}
                                >

                                <label for="incentivos_activos" class="config-toggle-label">
                                    Habilitar incentivos por ventas
                                </label>
                            </div>
                        </div>

                        <div
                            id="tabla-incentivos"
                            class="table-wrap config-table-wrap {{ !$configuracion->incentivos_activos ? 'config-disabled' : '' }}">

                            <table class="table config-table">

                                <thead>
                                    <tr>
                                        <th style="width:60%">Configuración</th>
                                        <th style="width:25%; text-align:center;">Valor</th>
                                        <th style="width:15%; text-align:center;">Unidad</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td>Tope mensual de ventas</td>
                                        <td>
                                            <input
                                                class="config-table-input"
                                                id="tope_ventas_incentivo"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                name="tope_ventas_incentivo"
                                                value="{{ old('tope_ventas_incentivo', $topeVentasIncentivo) }}"
                                                {{ !$configuracion->incentivos_activos ? 'disabled' : '' }}>
                                        </td>

                                        <td class="config-unit">
                                            RD$
                                        </td>
                                    </tr>

                                    <tr>

                                        <td>Porcentaje de incentivo sobre el excedente</td>
                                        <td>

                                            <input
                                                class="config-table-input"
                                                id="porcentaje_incentivo"
                                                type="number"
                                                step="0.01"
                                                min="0"
                                                max="100"
                                                name="porcentaje_incentivo"
                                                value="{{ old('porcentaje_incentivo', $porcentajeIncentivo) }}"
                                                {{ !$configuracion->incentivos_activos ? 'disabled' : '' }}>
                                        </td>

                                        <td class="config-unit">
                                            %
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <p class="config-nota" style="margin-top:14px;">
                            Cuando las comisiones mensuales de un fotógrafo superen el tope
                            establecido, el sistema calculará automáticamente el incentivo
                            sobre el monto excedente.
                        </p>

                        <div class="config-acciones">
                            <button
                                id="btnGuardarIncentivos"
                                type="submit"
                                class="btn btn-primary"
                                {{ !$configuracion->incentivos_activos ? 'disabled' : '' }}>
                                Guardar configuración
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        function activarTab(nombre) {
            document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
            document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));

            const tab = document.querySelector(`.tab[data-tab="${nombre}"]`);
            const panel = document.getElementById('panel-' + nombre);
            if (tab && panel) {
                tab.classList.add('active');
                panel.classList.add('active');
            }
        }

        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => activarTab(tab.dataset.tab));
        });

        // Si venimos de un redirect con ?tab=configuracion (tras guardar un
        // formulario de config), abrir esa pestaña en vez de la default.
        const tabUrl = new URLSearchParams(window.location.search).get('tab');
        if (tabUrl) {
            activarTab(tabUrl);
        }

        // Habilitar o deshabilitar los campos de incentivos
        const chkIncentivos = document.getElementById('incentivos_activos');
        const tope = document.getElementById('tope_ventas_incentivo');
        const porcentaje = document.getElementById('porcentaje_incentivo');

        function actualizarEstadoIncentivos() {
            const activo = chkIncentivos.checked;

            tope.disabled = !activo;
            porcentaje.disabled = !activo;
        }

        if (chkIncentivos) {
            actualizarEstadoIncentivos();
            chkIncentivos.addEventListener('change', actualizarEstadoIncentivos);
        }
    </script>
@endpush
