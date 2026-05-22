@extends('layouts.fotografo')
@section('title', 'Dashboard')

{{-- ═══════════════════════════════════
     SUBTITLE
═══════════════════════════════════ --}}
@section('subtitle')
    <span id="fecha-actual"></span>
    @if($tasaExito >= 80)
        &nbsp;<span class="perf-pill perf-pill--green">
            <svg width="9" height="9" viewBox="0 0 10 10" fill="currentColor">
                <path d="M5 1l1.1 3.4H9.5L6.7 6.6l1.1 3.4L5 7.8l-2.8 2.2 1.1-3.4L.5 4.4H3.9z"/>
            </svg>
            Excelente rendimiento
        </span>
    @endif
@endsection

@section('content')

    {{-- ═══════════════════════════════════
         HERO — perfil del fotógrafo
    ═══════════════════════════════════ --}}
    @php
        $persona = optional(optional(optional($fotografo->empleado)->usuario)->persona);
        $nombre  = $persona->nombre_completo
                ?? optional($fotografo->empleado)->nombre
                ?? Auth::user()->name
                ?? 'Fotógrafo';
        $inicial = strtoupper(mb_substr($nombre, 0, 1));
    @endphp

    <div class="foto-hero">
        <div class="foto-hero__avatar">
            <span>{{ $inicial }}</span>
            <div class="foto-hero__status {{ $reservasPendientes > 0 ? 'active' : '' }}"></div>
        </div>

        <div class="foto-hero__info">
            <h2 class="foto-hero__name">{{ $nombre }}</h2>
            <div class="foto-hero__meta">
                <span>
                    <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                        <circle cx="12" cy="13" r="4"/>
                    </svg>
                    Fotógrafo Profesional
                </span>
                @if($fotografo->experiencia_laboral)
                    <span>
                        <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                        </svg>
                        {{ $fotografo->experiencia_laboral }} año{{ $fotografo->experiencia_laboral != 1 ? 's' : '' }} de experiencia
                    </span>
                @endif
            </div>

            @if($fotografo->certificaciones)
                <div class="foto-hero__certs">
                    @foreach(array_slice($fotografo->certificaciones, 0, 4) as $cert)
                        <span class="cert-chip">{{ $cert }}</span>
                    @endforeach
                    @if(count($fotografo->certificaciones) > 4)
                        <span class="cert-chip cert-chip--more">+{{ count($fotografo->certificaciones) - 4 }} más</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="foto-hero__quick">
            <div class="quick-stat">
                <div class="quick-stat__val">{{ $tasaExito }}%</div>
                <div class="quick-stat__label">Tasa de éxito</div>
            </div>
        </div>
    </div>

    {{-- ═══════════════════════════════════
         KPIs
    ═══════════════════════════════════ --}}
    <div class="foto-kpi-grid">

        <div class="foto-kpi" style="animation-delay:.00s">
            <div class="foto-kpi__icon foto-kpi__icon--orange">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                    <circle cx="12" cy="13" r="4"/>
                </svg>
            </div>
            <div>
                <div class="foto-kpi__val">{{ $totalReservas }}</div>
                <div class="foto-kpi__label">Total de Reservas</div>
            </div>
        </div>

        <div class="foto-kpi" style="animation-delay:.06s">
            <div class="foto-kpi__icon foto-kpi__icon--amber">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                </svg>
            </div>
            <div>
                <div class="foto-kpi__val">{{ $reservasPendientes }}</div>
                <div class="foto-kpi__label">Pendientes</div>
            </div>
        </div>

        <div class="foto-kpi" style="animation-delay:.18s">
            <div class="foto-kpi__icon foto-kpi__icon--blue">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <div>
                <div class="foto-kpi__val">
                    {{ $reservasEsteMes }}
                    @if($deltaMes != 0)
                        <small class="kpi-delta-small {{ $deltaMes > 0 ? 'up' : 'down' }}">
                            {{ $deltaMes > 0 ? '↑' : '↓' }}{{ abs($deltaMes) }}%
                        </small>
                    @endif
                </div>
                <div class="foto-kpi__label">Este Mes</div>
            </div>
        </div>

        <div class="foto-kpi" style="animation-delay:.24s">
            <div class="foto-kpi__icon foto-kpi__icon--green">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="8" r="4"/>
                    <path d="M6 20v-2a4 4 0 014-4h4a4 4 0 014 4v2"/>
                    <polyline points="16 11 18 13 22 9"/>
                </svg>
            </div>
            <div>
                <div class="foto-kpi__val">{{ $sesionesFinalizadas }}</div>
                <div class="foto-kpi__label">Finalizadas</div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════
         FILA 1: Reservas por mes + Estado de sesiones
    ═══════════════════════════════════ --}}
    <div class="foto-charts-grid">

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Mis Reservas por Mes</div>
                    <div class="panel-sub">Últimos 6 meses</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:220px">
                    <canvas id="chartReservasMes"></canvas>
                </div>
            </div>
        </div>

        <div class="panel">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Estado de Sesiones</div>
                    <div class="panel-sub">Distribución por etapa</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:160px">
                    <canvas id="chartEstadosSesion"></canvas>
                </div>
                <div class="foto-donut-legend">
                    @php
                        $estadosConfig = [
                            'CONFIRMADA'         => ['label' => 'Confirmada',         'color' => '#3b82f6'],
                            'EN_PROCESO'         => ['label' => 'En Proceso',         'color' => '#f97316'],
                            'EN_EDICION'         => ['label' => 'En Edición',         'color' => '#a855f7'],
                            'GALERIA_DISPONIBLE' => ['label' => 'Galería Disponible', 'color' => '#06b6d4'],
                            'FINALIZADA'         => ['label' => 'Finalizada',         'color' => '#10b981'],
                            'CERRADA'            => ['label' => 'Cerrada',            'color' => '#6b7280'],
                        ];
                    @endphp
                    @foreach($estadosConfig as $key => $cfg)
                        @if($estadosSesion[$key] > 0)
                            <div class="legend-item">
                                <span class="legend-dot" style="background:{{ $cfg['color'] }}"></span>
                                <span class="legend-label">{{ $cfg['label'] }}</span>
                                <span class="legend-val">{{ $estadosSesion[$key] }}</span>
                            </div>
                        @endif
                    @endforeach
                </div>
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════
         FILA 2: Próximas sesiones + Paquetes
    ═══════════════════════════════════ --}}
    <div class="foto-row">

        <div class="panel foto-panel-lg">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Próximas Sesiones</div>
                    <div class="panel-sub">Reservas aprobadas con sesión agendada</div>
                </div>
                @if($sesionesProximas->count() > 0)
                    <span class="badge badge-foto">{{ $sesionesProximas->count() }} próximas</span>
                @endif
            </div>
            <div class="panel-body" style="padding:0">
                @forelse($sesionesProximas as $reserva)
                    @php
                        $fecha    = \Carbon\Carbon::parse($reserva->fecha_inicio);
                        $fechaFin = \Carbon\Carbon::parse($reserva->fecha_fin);
                        $esHoy      = $fecha->isToday();
                        $esMañana   = $fecha->isTomorrow();
                        $diasResta  = (int) \Carbon\Carbon::today()->diffInDays($fecha, false);
                        $estadoSesionClass = match($reserva->estado) {
                            'CONFIRMADA'         => 'badge-blue',
                            'EN_PROCESO'         => 'badge-foto',
                            'EN_EDICION'         => 'badge-purple',
                            'GALERIA_DISPONIBLE' => 'badge-cyan',
                            default              => 'badge-inactive',
                        };
                        $estadoSesionLabel = match($reserva->estado) {
                            'CONFIRMADA'         => 'Confirmada',
                            'EN_PROCESO'         => 'En Proceso',
                            'EN_EDICION'         => 'En Edición',
                            'GALERIA_DISPONIBLE' => 'Galería Lista',
                            default              => $reserva->estado,
                        };
                    @endphp
                    <div class="sesion-row">
                        <div class="sesion-fecha-col">
                            <div class="sesion-dia">{{ $fecha->format('d') }}</div>
                            <div class="sesion-mes">{{ $fecha->translatedFormat('M') }}</div>
                        </div>
                        <div class="sesion-info">
                            <div class="sesion-titulo">
                                {{ optional($reserva->paquete)->nombre ?? 'Sesión fotográfica' }}
                            </div>
                            <div class="sesion-meta">
                                <span>
                                    <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                        <circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>
                                    </svg>
                                    {{ $fecha->format('H:i') }} – {{ $fechaFin->format('H:i') }}
                                </span>
                                @if($reserva->lugar)
                                    <span>
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/>
                                            <circle cx="12" cy="10" r="3"/>
                                        </svg>
                                        {{ $reserva->lugar }}
                                    </span>
                                @endif
                                @if($reserva->tipo)
                                    <span>
                                        <svg width="11" height="11" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                            <path d="M4 15s1-1 4-1 5 2 8 2 4-1 4-1V3s-1 1-4 1-5-2-8-2-4 1-4 1z"/>
                                            <line x1="4" y1="22" x2="4" y2="15"/>
                                        </svg>
                                        {{ $reserva->tipo }}
                                    </span>
                                @endif
                            </div>
                        </div>
                        <div class="sesion-right">
                            @if($esHoy)
                                <span class="sesion-badge sesion-badge--hoy">Hoy</span>
                            @elseif($esMañana)
                                <span class="sesion-badge sesion-badge--mañana">Mañana</span>
                            @elseif($diasResta > 0 && $diasResta <= 7)
                                <span class="sesion-badge sesion-badge--pronto">{{ $diasResta }}d</span>
                            @else
                                <span class="sesion-badge">{{ $fecha->format('d M') }}</span>
                            @endif
                            <span class="badge {{ $estadoSesionClass }}">{{ $estadoSesionLabel }}</span>
                        </div>
                    </div>
                @empty
                    <div class="foto-empty">
                        <svg width="36" height="36" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M23 19a2 2 0 01-2 2H3a2 2 0 01-2-2V8a2 2 0 012-2h4l2-3h6l2 3h4a2 2 0 012 2z"/>
                            <circle cx="12" cy="13" r="4"/>
                        </svg>
                        <p>No tienes sesiones próximas agendadas.</p>
                    </div>
                @endforelse
            </div>
        </div>

        <div class="panel foto-panel-sm">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Tipos de Sesión</div>
                    <div class="panel-sub">Catálogos más frecuentes</div>
                </div>
            </div>
            <div class="panel-body">
                @if($tiposSesion->count() > 0)
                    <div class="chart-container" style="height:160px; margin-bottom:16px">
                        <canvas id="chartTipos"></canvas>
                    </div>
                    @foreach($tiposSesion as $tipo)
                        @php
                            $pct = $totalReservas > 0
                                ? min(100, round($tipo->total / $totalReservas * 100))
                                : 0;
                        @endphp
                        <div class="tipo-item">
                            <div class="tipo-top">
                                <span>{{ $tipo->nombre }}</span>
                                <span class="tipo-count">{{ $tipo->total }} <small>({{ $pct }}%)</small></span>
                            </div>
                            <div class="progress-track">
                                <div class="progress-fill" style="width:{{ $pct }}%"></div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="foto-empty" style="padding:20px 0">
                        <p>Sin datos de paquetes aún.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

    {{-- ═══════════════════════════════════
         FILA 3: Actividad semanal + Certificaciones
    ═══════════════════════════════════ --}}
    <div class="foto-row">

        <div class="panel foto-panel-sm">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Actividad Semanal</div>
                    <div class="panel-sub">Sesiones por día (últimos 3 meses)</div>
                </div>
            </div>
            <div class="panel-body">
                <div class="chart-container" style="height:200px">
                    <canvas id="chartSemana"></canvas>
                </div>
            </div>
        </div>

        <div class="panel foto-panel-lg">
            <div class="panel-header">
                <div>
                    <div class="panel-title">Mis Certificaciones</div>
                    <div class="panel-sub">Credenciales registradas</div>
                </div>
                @if($fotografo->certificaciones)
                    <span class="badge badge-active">{{ count($fotografo->certificaciones) }} total</span>
                @endif
            </div>
            <div class="panel-body">
                @if($fotografo->certificaciones && count($fotografo->certificaciones) > 0)
                    <div class="certs-grid">
                        @foreach($fotografo->certificaciones as $idx => $cert)
                            <div class="cert-card" style="animation-delay:{{ $idx * 0.05 }}s">
                                <div class="cert-card__icon">
                                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">
                                        <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                                        <polyline points="9 12 11 14 15 10"/>
                                    </svg>
                                </div>
                                <div class="cert-card__name">{{ $cert }}</div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="foto-empty">
                        <svg width="32" height="32" fill="none" stroke="currentColor" stroke-width="1.5" viewBox="0 0 24 24">
                            <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                        </svg>
                        <p>No hay certificaciones registradas.</p>
                    </div>
                @endif
            </div>
        </div>

    </div>

@endsection

@push('scripts')
    @php
        // Preparar variables PHP para JS — sin lógica dentro de @json()
        $js_mesesLabels      = $mesesLabels    ?? [];
        $js_reservasPorMes   = $reservasPorMes ?? [];

        $js_estadosLabels = ['Confirmada','En Proceso','En Edición','Galería Lista','Finalizada','Cerrada'];
        $js_estadosColors = ['#3b82f6','#f97316','#a855f7','#06b6d4','#10b981','#6b7280'];
        $js_estadosData   = [
            $estadosSesion['CONFIRMADA']         ?? 0,
            $estadosSesion['EN_PROCESO']         ?? 0,
            $estadosSesion['EN_EDICION']         ?? 0,
            $estadosSesion['GALERIA_DISPONIBLE'] ?? 0,
            $estadosSesion['FINALIZADA']         ?? 0,
            $estadosSesion['CERRADA']            ?? 0,
        ];

        $js_tiposLabels  = $tiposSesion->pluck('nombre')->values()->all();
        $js_tiposData    = $tiposSesion->pluck('total')->values()->all();
        $js_semanaLabels = $semanaLabels ?? ['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'];
        $js_semanaData   = $semanaData   ?? [];
    @endphp
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const O     = '#e87722';
        const MUTED = '#7a6055';

        // Fecha en header
        document.getElementById('fecha-actual').textContent =
            new Date().toLocaleDateString('es-DO', {
                weekday: 'long', year: 'numeric', month: 'long', day: 'numeric'
            });

        // ── Datos desde PHP ──────────────────────────────────
        const mesesLabels    = @json($js_mesesLabels);
        const reservasPorMes = @json($js_reservasPorMes);
        const estadosLabels  = @json($js_estadosLabels);
        const estadosColors  = @json($js_estadosColors);
        const estadosData    = @json($js_estadosData);
        const tiposLabels    = @json($js_tiposLabels);
        const tiposData      = @json($js_tiposData);
        const semanaLabels   = @json($js_semanaLabels);
        const semanaData     = @json($js_semanaData);

        // ── Reservas por Mes ────────────────────────────────
        const ctxMes  = document.getElementById('chartReservasMes').getContext('2d');
        const gradMes = ctxMes.createLinearGradient(0, 0, 0, 220);
        gradMes.addColorStop(0, 'rgba(232,119,34,0.20)');
        gradMes.addColorStop(1, 'rgba(232,119,34,0.00)');

        new Chart(ctxMes, {
            type: 'line',
            data: {
                labels: mesesLabels,
                datasets: [{
                    label: 'Reservas',
                    data: reservasPorMes,
                    borderColor: O,
                    backgroundColor: gradMes,
                    borderWidth: 2.5,
                    fill: true,
                    tension: 0.4,
                    pointRadius: 4,
                    pointHoverRadius: 7,
                    pointBackgroundColor: O,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ' + ctx.raw + ' reservas' } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: MUTED, font: { size: 11 } } },
                    y: {
                        grid: { color: 'rgba(200,96,21,0.06)' },
                        ticks: { color: MUTED, font: { size: 11 }, stepSize: 1, precision: 0 }
                    }
                }
            }
        });

        // ── Estado de Sesiones (Donut) ───────────────────────
        // Filtrar estados con valor 0 para no saturar el donut
        const filteredLabels = estadosLabels.filter((_, i) => estadosData[i] > 0);
        const filteredColors = estadosColors.filter((_, i) => estadosData[i] > 0);
        const filteredData   = estadosData.filter(v => v > 0);

        if (filteredData.length > 0) {
            new Chart(document.getElementById('chartEstadosSesion').getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: filteredLabels,
                    datasets: [{
                        data: filteredData,
                        backgroundColor: filteredColors,
                        borderWidth: 0,
                        hoverOffset: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    cutout: '70%',
                    plugins: {
                        legend: { display: false },
                        tooltip: { callbacks: { label: ctx => ' ' + ctx.label + ': ' + ctx.raw } }
                    }
                }
            });
        }

        // ── Tipos de Sesión (Polar) ──────────────────────────
        if (tiposLabels.length > 0) {
            new Chart(document.getElementById('chartTipos').getContext('2d'), {
                type: 'polarArea',
                data: {
                    labels: tiposLabels,
                    datasets: [{
                        data: tiposData,
                        backgroundColor: [
                            'rgba(232,119,34,0.75)',
                            'rgba(240,146,74,0.65)',
                            'rgba(201,96,21,0.70)',
                            'rgba(253,217,172,0.85)',
                            'rgba(255,243,232,0.90)',
                        ],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { display: false } },
                    scales: {
                        r: {
                            ticks: { display: false },
                            grid: { color: 'rgba(200,96,21,0.08)' }
                        }
                    }
                }
            });
        }

        // ── Actividad Semanal (Bar) ──────────────────────────
        const maxSemana = Math.max(...semanaData, 1);
        new Chart(document.getElementById('chartSemana').getContext('2d'), {
            type: 'bar',
            data: {
                labels: semanaLabels,
                datasets: [{
                    label: 'Sesiones',
                    data: semanaData,
                    backgroundColor: semanaData.map(v => v === maxSemana && v > 0 ? O : 'rgba(232,119,34,0.22)'),
                    borderRadius: 6,
                    borderSkipped: false,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: { callbacks: { label: ctx => ' ' + ctx.raw + ' sesiones' } }
                },
                scales: {
                    x: { grid: { display: false }, ticks: { color: MUTED, font: { size: 11 } } },
                    y: {
                        grid: { color: 'rgba(200,96,21,0.06)' },
                        ticks: { color: MUTED, font: { size: 11 }, stepSize: 1, precision: 0 }
                    }
                }
            }
        });
    </script>
@endpush
