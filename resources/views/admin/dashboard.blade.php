@extends('layouts.admin')
@section('title', 'Dashboard')

{{--
     TOPBAR ACTIONS
--}}
@section('topbar-actions')
    &nbsp;
    @if($crecimiento >= 0)
        <span class="perf-pill">
            <svg width="10" height="10" viewBox="0 0 10 10" fill="currentColor"><path d="M5 1l1.1 3.4H9.5L6.7 6.6l1.1 3.4L5 7.8l-2.8 2.2 1.1-3.4L.5 4.4H3.9z"/></svg>
            Crecimiento: +{{ $crecimiento }}%
        </span>
    @endif
@endsection

@section('subtitle')
    <span id="fecha-actual"></span>
@endsection

@section('content')

    {{-- KPIs — datos reales del controller --}}
    <div class="kpi-grid">

        {{-- Ingresos Totales --}}
        <div class="kpi-card" style="animation-delay:0s">
            <div class="kpi-top">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M12 2v20M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/>
                    </svg>
                </div>
                <span class="kpi-delta {{ $deltaIngresos >= 0 ? 'up' : 'down' }}">
                    {{ $deltaIngresos >= 0 ? '↑' : '↓' }} {{ abs($deltaIngresos) }}%
                </span>
            </div>
            <div class="kpi-value">${{ number_format($ingresosMes, 0, ',', '.') }}</div>
            <div class="kpi-label">Ingresos Totales</div>
            <div class="kpi-compare">vs ${{ number_format($ingresosMesAnt, 0, ',', '.') }} mes anterior</div>
            <div class="sparkline-wrap"><canvas id="sp1" height="36"></canvas></div>
        </div>

        {{-- Clientes Nuevos --}}
        <div class="kpi-card" style="animation-delay:0.07s">
            <div class="kpi-top">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/>
                        <path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/>
                    </svg>
                </div>
                <span class="kpi-delta {{ $deltaClientes >= 0 ? 'up' : 'down' }}">
                    {{ $deltaClientes >= 0 ? '↑' : '↓' }} {{ abs($deltaClientes) }}%
                </span>
            </div>
            <div class="kpi-value">{{ $clientesNuevosMes }}</div>
            <div class="kpi-label">Clientes Nuevos</div>
            <div class="kpi-compare">vs {{ $clientesNuevosAnt }} mes anterior</div>
            <div class="sparkline-wrap"><canvas id="sp2" height="36"></canvas></div>
        </div>

        {{-- Crecimiento Mensual --}}
        <div class="kpi-card" style="animation-delay:0.14s">
            <div class="kpi-top">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="22 7 13.5 15.5 8.5 10.5 2 17"/>
                        <polyline points="16 7 22 7 22 13"/>
                    </svg>
                </div>
                <span class="kpi-delta {{ $crecimiento >= 0 ? 'up' : 'down' }}">
                    {{ $crecimiento >= 0 ? '↑' : '↓' }} {{ abs($crecimiento) }}%
                </span>
            </div>
            <div class="kpi-value">{{ $crecimiento >= 0 ? '+' : '' }}{{ $crecimiento }}%</div>
            <div class="kpi-label">Crecimiento Mensual</div>
            <div class="kpi-compare">vs mes anterior en ingresos</div>
            <div class="sparkline-wrap"><canvas id="sp3" height="36"></canvas></div>
        </div>

        {{-- Reservas Totales --}}
        <div class="kpi-card" style="animation-delay:0.21s">
            <div class="kpi-top">
                <div class="kpi-icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                        <rect x="3" y="4" width="18" height="18" rx="2"/>
                        <line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/>
                        <line x1="3" y1="10" x2="21" y2="10"/>
                    </svg>
                </div>
                <span class="kpi-delta {{ $deltaReservas >= 0 ? 'up' : 'down' }}">
                    {{ $deltaReservas >= 0 ? '↑' : '↓' }} {{ abs($deltaReservas) }}%
                </span>
            </div>
            <div class="kpi-value">{{ $totalReservas }}</div>
            <div class="kpi-label">Reservas Totales</div>
            <div class="kpi-compare">vs {{ $totalReservasAnt }} mes anterior</div>
            <div class="sparkline-wrap"><canvas id="sp4" height="36"></canvas></div>
        </div>

    </div>

    {{--
         Ingresos por Mes + Estado de Reservas
    --}}
    <div class="charts-grid">

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Ingresos por Mes</div>
                    <div class="panel-sub">Año actual vs año anterior</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:260px">
                    <canvas id="chartIngresos"></canvas>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Estado de Reservas</div>
                    <div class="panel-sub">Distribución mes actual</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:200px">
                    <canvas id="chartEstados"></canvas>
                </div>
                <div class="estados-legend">
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#10b981"></span>
                        <span class="legend-label">Completadas</span>
                        <span class="legend-val">{{ $estadosReservas['aprobada'] }}</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#f97316"></span>
                        <span class="legend-label">Pendientes</span>
                        <span class="legend-val">{{ $estadosReservas['pendiente'] }}</span>
                    </div>
                    <div class="legend-item">
                        <span class="legend-dot" style="background:#ef4444"></span>
                        <span class="legend-label">Canceladas</span>
                        <span class="legend-val">{{ $estadosReservas['cancelada'] }}</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{--
         Reservas por Día + Rendimiento Paquetes
    --}}
    <div class="charts-row">

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Reservas por Día</div>
                    <div class="panel-sub">Últimos 30 días</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:180px">
                    <canvas id="chartReservasDia"></canvas>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Rendimiento de Paquetes</div>
                    <div class="panel-sub">Top 5 más reservados este mes</div>
                </div>
            </div>
            <div class="panel-body">
                @if(count($paquetesLabels) > 0)
                    <div class="chart-container" style="height:180px">
                        <canvas id="chartPaquetes"></canvas>
                    </div>
                @else
                    <div style="text-align:center;color:var(--muted);padding:40px 0;font-size:13px">
                        Sin datos de paquetes este mes
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{--
         Objetivo del Mes + Horas Pico
    --}}
    <div class="charts-row">

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Objetivo del Mes</div>
                    <div class="panel-sub">Progreso hacia metas</div>
                </div>
                @if(!$metas['meta_configurada'])
                    <a href="{{ route('admin.metas.edit') }}" class="btn-secondary">
                        Configurar Metas
                    </a>
                @endif
            </div>
            <div class="panel-body">
                @php
                    $objetivos = [
                        ['label'=>'Ingresos',        'actual'=>$ingresosMes,       'meta'=>$metas['ingresos'],        'unit'=>'$'],
                        ['label'=>'Reservas Totales', 'actual'=>$totalReservas,     'meta'=>$metas['reservas'],        'unit'=>''],
                        ['label'=>'Clientes Nuevos',  'actual'=>$clientesNuevosMes, 'meta'=>$metas['clientes_nuevos'], 'unit'=>''],
                    ];
                @endphp
                @foreach($objetivos as $obj)
                    @php
                        $pct = $obj['meta'] > 0 ? min(100, round($obj['actual'] / $obj['meta'] * 100)) : 0;
                        $cls = $pct >= 80 ? '' : ($pct >= 50 ? 'warning' : 'danger');
                    @endphp
                    <div class="goal-item">
                        <div class="goal-top">
                            <span>{{ $obj['label'] }}</span>
                            <span>
                                {{ $obj['unit'] }}{{ number_format($obj['actual']) }}
                                / {{ $obj['unit'] }}{{ number_format($obj['meta']) }}
                                &nbsp;<strong class="{{ $pct >= 80 ? 'goal-pct-ok' : 'goal-pct-low' }}">{{ $pct }}%</strong>
                            </span>
                        </div>
                        <div class="progress-track">
                            <div class="progress-fill {{ $cls }}" style="width:{{ $pct }}%"></div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Horas Pico de Reservas</div>
                    <div class="panel-sub">Actividad por hora del día — mes actual</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:200px">
                    <canvas id="chartHoras"></canvas>
                </div>
                @php
                    $maxVal  = count($horasData) ? max($horasData) : 0;
                    $maxIdx  = $maxVal ? array_search($maxVal, $horasData) : 0;
                    $labelPico = $horasLabels[$maxIdx] ?? '—';
                @endphp
                <div class="horas-note">
                    Pico máximo: <strong>{{ $labelPico }}</strong>
                    ({{ $maxVal }} reservas)
                </div>
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const O     = '#e87722';
        const O3    = '#f0924a';
        const MUTED = '#7a6055';

        document.getElementById('fecha-actual').textContent =
            new Date().toLocaleDateString('es-DO', {weekday:'long', year:'numeric', month:'long', day:'numeric'});

        function exportarReporte() {
            alert('Conectar con ruta Laravel /admin/export');
        }

        // ── Datos reales desde PHP
        const mesesLabels      = @json($mesesLabels);
        const ingresosActual   = @json($ingresosActual);
        const ingresosAnterior = @json($ingresosAnterior);

        const estadosData = [
            {{ $estadosReservas['aprobada'] }},
            {{ $estadosReservas['pendiente'] }},
            {{ $estadosReservas['cancelada'] }}
        ];

        const diasLabels     = @json($diasLabels);
        const reservasDia    = @json($reservasDia);
        const paquetesLabels = @json($paquetesLabels);
        const paquetesData   = @json($paquetesData);
        const horasLabels    = @json($horasLabels);
        const horasData      = @json($horasData);

        // ── Sparklines (tendencia con datos reales) ─────
        [
            { id: 'sp1', data: ingresosActual.slice(-10) },
            { id: 'sp2', data: reservasDia.slice(-10) },
            { id: 'sp3', data: ingresosActual.slice(-10) },
            { id: 'sp4', data: reservasDia.slice(-10) },
        ].forEach(({ id, data }) => {
            const ctx = document.getElementById(id);
            if (!ctx || !data.length) return;
            new Chart(ctx, {
                type: 'line',
                data: {
                    labels: data.map((_, i) => i),
                    datasets: [{ data, borderColor: O, borderWidth: 2, fill: true,
                        backgroundColor: 'rgba(232,119,34,0.08)', pointRadius: 0, tension: 0.4 }]
                },
                options: { responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: { x: { display: false }, y: { display: false } } }
            });
        });

        // ── Ingresos por Mes
        const ctxIng  = document.getElementById('chartIngresos').getContext('2d');
        const gradIng = ctxIng.createLinearGradient(0, 0, 0, 260);
        gradIng.addColorStop(0, 'rgba(232,119,34,0.18)');
        gradIng.addColorStop(1, 'rgba(232,119,34,0)');

        new Chart(ctxIng, {
            type: 'line',
            data: {
                labels: mesesLabels,
                datasets: [
                    { label: 'Año actual',   data: ingresosActual,   borderColor: O,     backgroundColor: gradIng,
                        borderWidth: 2.5, fill: true,  tension: 0.4, pointRadius: 3, pointHoverRadius: 6, pointBackgroundColor: O },
                    { label: 'Año anterior', data: ingresosAnterior, borderColor: MUTED, backgroundColor: 'transparent',
                        borderWidth: 1.5, fill: false, tension: 0.4, pointRadius: 0, borderDash: [4, 4] }
                ]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'bottom', labels: { font: { size: 12 }, color: MUTED, boxWidth: 12 } },
                    tooltip: { callbacks: { label: ctx => ' $' + ctx.raw.toLocaleString() } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: MUTED, font: { size: 11 } } },
                    y: { grid: { color: 'rgba(200,96,21,0.06)' },
                        ticks: { color: MUTED, font: { size: 11 }, callback: v => '$' + v.toLocaleString() } }
                }
            }
        });

        // ── Estado de Reservas
        new Chart(document.getElementById('chartEstados').getContext('2d'), {
            type: 'doughnut',
            data: {
                labels: ['Aprobadas', 'Pendientes', 'Canceladas', 'En Sesión'],
                datasets: [{ data: estadosData,
                    backgroundColor: ['#10b981', '#f97316', '#ef4444', O],
                    borderWidth: 0, hoverOffset: 6 }]
            },
            options: {
                responsive: true, maintainAspectRatio: false, cutout: '72%',
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.raw } }
                }
            }
        });

        // ── Reservas por Día
        new Chart(document.getElementById('chartReservasDia').getContext('2d'), {
            type: 'bar',
            data: {
                labels: diasLabels,
                datasets: [{ label: 'Reservas', data: reservasDia,
                    backgroundColor: ctx => {
                        const g = ctx.chart.ctx.createLinearGradient(0, 0, 0, 180);
                        g.addColorStop(0, O); g.addColorStop(1, 'rgba(232,119,34,0.3)'); return g;
                    },
                    borderRadius: 4, borderSkipped: false }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: MUTED, font: { size: 9 }, maxRotation: 0,
                            callback: (_, i) => i % 5 === 0 ? diasLabels[i] : '' } },
                    y: { grid: { color: 'rgba(200,96,21,0.06)' }, ticks: { color: MUTED, font: { size: 11 } } }
                }
            }
        });

        // ── Rendimiento de Paquetes
        if (paquetesLabels.length > 0) {
            new Chart(document.getElementById('chartPaquetes').getContext('2d'), {
                type: 'bar',
                data: {
                    labels: paquetesLabels,
                    datasets: [{ label: 'Reservas', data: paquetesData,
                        backgroundColor: [O, O3, '#fcd9ac', '#fde8d0', '#fff3e8'],
                        borderRadius: 6, borderSkipped: false }]
                },
                options: {
                    indexAxis: 'y', responsive: true, maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        x: { grid: { color: 'rgba(200,96,21,0.06)' }, ticks: { color: MUTED, font: { size: 11 } } },
                        y: { grid: { display: false }, ticks: { color: MUTED, font: { size: 11 } } }
                    }
                }
            });
        }

        // ── Horas Pico
        new Chart(document.getElementById('chartHoras').getContext('2d'), {
            type: 'line',
            data: {
                labels: horasLabels,
                datasets: [{ label: 'Reservas', data: horasData,
                    borderColor: O, backgroundColor: 'rgba(232,119,34,0.1)',
                    borderWidth: 2.5, fill: true, tension: 0.4,
                    pointRadius: 3, pointBackgroundColor: O }]
            },
            options: {
                responsive: true, maintainAspectRatio: false,
                plugins: { legend: { display: false } },
                scales: {
                    x: { grid: { display: false }, ticks: { color: MUTED, font: { size: 10 } } },
                    y: { grid: { color: 'rgba(200,96,21,0.06)' }, ticks: { color: MUTED, font: { size: 10 } } }
                }
            }
        });
    </script>
@endpush
