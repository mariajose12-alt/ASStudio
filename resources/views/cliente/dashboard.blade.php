@extends('layouts.cliente')
@section('title', 'Inicio')

@section('topbar-actions')
    <a href="{{ route('cliente.reservas.paso1') }}" class="btn-nueva-reserva">
        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva reserva
    </a>
@endsection

@section('content')

    {{-- ══════════════════════════════════════════
         HERO — perfil + stats integrados
    ══════════════════════════════════════════ --}}
    <div class="dash-hero">
        <div class="dash-hero__left">
            <div class="dash-hero__avatar">
                {{ strtoupper(substr($usuario->persona->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->persona->apellido, 0, 1)) }}
                <span class="dash-hero__status active"></span>
            </div>
            <div class="dash-hero__identity">
                <h1 class="dash-hero__name">
                    {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
                </h1>
                <div class="dash-hero__meta">
                    @if($usuario->persona->telefono)
                        <span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                            </svg>
                            {{ $usuario->persona->telefono }}
                        </span>
                    @endif
                    <span>
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                        {{ $usuario->email }}
                    </span>
                    <span>
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                        Desde {{ $usuario->created_at->translatedFormat('M Y') }}
                    </span>
                </div>
            </div>
        </div>

        <div class="dash-hero__kpis">
            <div class="dash-hero__kpi">
                <span class="dash-hero__kpi-val">{{ $totalReservas }}</span>
                <span class="dash-hero__kpi-label">Total</span>
            </div>
            <div class="dash-hero__kpi-divider"></div>
            <div class="dash-hero__kpi">
                <span class="dash-hero__kpi-val dash-hero__kpi-val--amber">{{ $reservasPendientes }}</span>
                <span class="dash-hero__kpi-label">Pendientes</span>
            </div>
            <div class="dash-hero__kpi-divider"></div>
            <div class="dash-hero__kpi">
                <span class="dash-hero__kpi-val dash-hero__kpi-val--green">{{ $reservasCompletadas }}</span>
                <span class="dash-hero__kpi-label">Completadas</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GRID PRINCIPAL — gráfico + próximas sesiones
    ══════════════════════════════════════════ --}}
    <div class="dash-main-grid">

        {{-- Gráfico de sesiones --}}
        <div class="card dash-chart-card">
            <div class="card-header">
                <div>
                    <h2>Actividad reciente</h2>
                    <p class="dash-chart-subtitle">Sesiones completadas · últimos 6 meses</p>
                </div>
                <div class="dash-chart-total">
                    <span class="dash-chart-total__num">{{ $reservasCompletadas }}</span>
                    <span class="dash-chart-total__label">sesiones</span>
                </div>
            </div>
            <div class="card-body">
                <div class="chart-wrap">
                    <canvas id="sesionesChart"
                            role="img"
                            aria-label="Gráfico de sesiones completadas en los últimos 6 meses">
                    </canvas>
                </div>
            </div>
        </div>

        {{-- Próximas sesiones --}}
        <div class="card dash-proximas-card">
            <div class="card-header">
                <h2>Próximas sesiones</h2>
                <a href="{{ route('cliente.reservas.index') }}" class="btn btn-outline btn-sm">
                    Ver todas
                </a>
            </div>

            @forelse($proximasReservas as $reserva)
                @php
                    $fecha      = \Carbon\Carbon::parse($reserva->fecha_inicio);
                    $hoy        = \Carbon\Carbon::today();
                    $diffDias   = $hoy->diffInDays($fecha, false);
                    $etiqueta   = match(true) {
                        $diffDias === 0 => 'hoy',
                        $diffDias === 1 => 'mañana',
                        $diffDias <= 7  => 'pronto',
                        default         => null,
                    };
                @endphp
                <div class="sesion-row">
                    <div class="sesion-fecha-col">
                        <div class="sesion-dia">{{ $fecha->format('d') }}</div>
                        <div class="sesion-mes">{{ $fecha->translatedFormat('M') }}</div>
                    </div>
                    <div class="sesion-info">
                        <div class="sesion-titulo">
                            {{ $reserva->paquete->nombre ?? 'Sesión fotográfica' }}
                        </div>
                        <div class="sesion-meta">
                            <span>
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                {{ $fecha->format('g:i A') }}
                            </span>
                        </div>
                    </div>
                    <div class="sesion-right">
                        @if($etiqueta && strtolower($reserva->estado) === 'aprobada')
                            <span class="sesion-badge sesion-badge--{{ $etiqueta }}">{{ $etiqueta }}</span>
                        @endif
                        <span class="estado-badge badge-{{ strtolower($reserva->estado) }}">
                            {{ ucfirst($reserva->estado) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="dash-empty-state">
                    <div class="dash-empty-state__icon">
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <p class="dash-empty-state__text">Sin sesiones próximas</p>
                    <a href="{{ route('cliente.reservas.paso1') }}" class="btn btn-primary btn-sm">
                        Reservar ahora
                    </a>
                </div>
            @endforelse
        </div>
    </div>
    {{-- ══════════════════════════════════════════
             GALERÍA PREVIEW — mock temporal (sin service)
        ══════════════════════════════════════════ --}}
    <div class="card gal-section" style="margin-top: 20px;">
        <div class="card-header">
            <div>
                <div class="gal-eyebrow">Tu galería</div>
                <h2>Sesiones recientes</h2>
            </div>
            <a href="{{ route('cliente.galeria') }}" class="btn btn-outline btn-sm">
                Ver galería completa
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

        <div class="card-body">
            <div class="gal-grid">

                <div class="gal-item gal-item--tall">
                    <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=600&q=80&auto=format&fit=crop" alt="Retrato" loading="lazy">
                    <div class="gal-item__overlay">
                        <span class="gal-item__label">Retrato · Mar 2025</span>
                    </div>
                </div>

                <div class="gal-item">
                    <img src="https://images.unsplash.com/photo-1606216794074-735e91aa2c92?w=400&q=80&auto=format&fit=crop" alt="Sesión de pareja" loading="lazy">
                    <div class="gal-item__overlay">
                        <span class="gal-item__label">Pareja · Feb 2025</span>
                    </div>
                </div>

                <div class="gal-item">
                    <img src="https://images.unsplash.com/photo-1538678867871-8a43e7487746?q=80&w=688&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Maternidad" loading="lazy">
                    <div class="gal-item__overlay">
                        <span class="gal-item__label">Maternidad · Feb 2025</span>
                    </div>
                </div>

                <div class="gal-item">
                    <img src="https://images.unsplash.com/photo-1588979355313-6711a095465f?q=80&w=672&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Familia" loading="lazy">
                    <div class="gal-item__overlay">
                        <span class="gal-item__label">Familia · Ene 2025</span>
                    </div>
                </div>

                <a href="{{ route('cliente.galeria') }}" class="gal-item gal-item--more">
                    <span class="gal-more-num">+2</span>
                    <span class="gal-more-txt">sesiones</span>
                </a>

            </div>

            <div class="gal-footer">
            <span class="gal-footer-meta">
                <strong>29</strong> fotos en tu galería · <strong>6</strong> sesiones
            </span>
                <a href="{{ route('cliente.galeria') }}" class="btn btn-outline btn-sm">
                    <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                    </svg>
                    Descargar todo
                </a>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const meses   = @json($sesionesPorMes['meses']);
        const totales = @json($sesionesPorMes['totales']);

        const style   = getComputedStyle(document.documentElement);
        const accent  = style.getPropertyValue('--blue-mid').trim()  || '#e87722';
        const accentBg = style.getPropertyValue('--blue-pale').trim() || '#fff3e8';
        const mutedTxt = style.getPropertyValue('--muted').trim()     || '#7a6055';
        const borderCl = style.getPropertyValue('--border').trim()    || 'rgba(200,96,21,0.12)';

        new Chart(document.getElementById('sesionesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Sesiones',
                    data: totales,
                    borderColor: accent,
                    backgroundColor: 'rgba(232,119,34,0.07)',
                    borderWidth: 2,
                    pointBackgroundColor: accent,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7,
                    tension: 0.4,
                    fill: true,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#1a0d00',
                        titleColor: '#e87722',
                        bodyColor: '#c8bfaf',
                        borderColor: 'rgba(232,119,34,0.2)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => `  ${ctx.parsed.y} sesión${ctx.parsed.y !== 1 ? 'es' : ''}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: 'rgba(200,96,21,0.08)' },
                        border: { display: false },
                        ticks: { font: { size: 11, family: 'DM Sans, sans-serif' }, color: mutedTxt }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(200,96,21,0.08)' },
                        border: { display: false },
                        ticks: {
                            stepSize: 1,
                            font: { size: 11, family: 'DM Sans, sans-serif' },
                            color: mutedTxt
                        }
                    }
                }
            }
        });
    </script>
@endpush
