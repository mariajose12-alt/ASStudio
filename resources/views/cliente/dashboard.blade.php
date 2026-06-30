@extends('layouts.cliente')
@section('title', 'Inicio')

@section('content')

    {{-- ══════════════════════════════════════════
         HERO — saludo + KPIs
         Móvil: bloque navy vertical
         Desktop: franja horizontal (ver mobile.css)
    ══════════════════════════════════════════ --}}
    <div class="dash-hero">
        <div class="dash-hero__left">
            {{-- Texto visible solo en móvil --}}
            <div class="dash-hero__greeting">Buenos días,</div>
            <div class="dash-hero__name">
                {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
            </div>

            {{-- Identidad para desktop (avatar + nombre + meta) --}}
            <div class="dash-hero__identity">
                <div class="dash-hero__avatar" aria-hidden="true">
                    {{ strtoupper(substr($usuario->persona->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->persona->apellido, 0, 1)) }}
                </div>
                <div>
                    <div class="dash-hero__identity-name">
                        {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
                    </div>
                    <div class="dash-hero__identity-meta">
                        {{ $usuario->email }} · Desde {{ $usuario->created_at->translatedFormat('M Y') }}
                        @if($usuario->persona->telefono)
                            · {{ $usuario->persona->telefono }}
                        @endif
                    </div>
                </div>
            </div>
        </div>

        <div class="dash-hero__kpis">
            <div class="dash-hero__kpi">
                <span class="dash-hero__kpi-val">{{ $totalReservas }}</span>
                <span class="dash-hero__kpi-label">Total</span>
            </div>
            <div class="dash-hero__kpi">
                <span class="dash-hero__kpi-val dash-hero__kpi-val--amber">{{ $reservasPendientes }}</span>
                <span class="dash-hero__kpi-label">Pendientes</span>
            </div>
            <div class="dash-hero__kpi">
                <span class="dash-hero__kpi-val dash-hero__kpi-val--green">{{ $reservasCompletadas }}</span>
                <span class="dash-hero__kpi-label">Completadas</span>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GRID PRINCIPAL — gráfico + próximas sesiones
         Móvil: apilado verticalmente
         Desktop: 2 columnas (ver .dash-main-grid en mobile.css)
    ══════════════════════════════════════════ --}}
    <div class="dash-main-grid">

        {{-- Próximas sesiones (primero en móvil: es la info más accionable) --}}
        <div class="card dash-proximas-card">
            <div class="card-header">
                <h2>Próximas sesiones</h2>
                <a href="{{ route('cliente.reservas.index') }}" class="btn btn-outline btn-sm">
                    Ver todas
                </a>
            </div>

            @forelse($proximasReservas as $reserva)
                @php
                    $fecha    = \Carbon\Carbon::parse($reserva->fecha_inicio);
                    $hoy      = \Carbon\Carbon::today();
                    $diffDias = $hoy->diffInDays($fecha, false);
                    $etiqueta = match(true) {
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
                                <svg width="11" height="11" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
                        <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
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
    </div>

    {{-- ══════════════════════════════════════════
         GALERÍA PREVIEW
         Móvil: grid 3 columnas compacto
         Desktop: mosaico 4 columnas con destacado
    ══════════════════════════════════════════ --}}
    <div class="card gal-section" style="margin-top: 12px;">
        <div class="card-header">
            <div>
                <div class="gal-eyebrow">Tu galería</div>
                <h2>Sesiones recientes</h2>
            </div>
            <a href="{{ route('cliente.galeria') }}" class="btn btn-outline btn-sm">
                Ver galería
                <svg width="12" height="12" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/>
                </svg>
            </a>
        </div>

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
                <strong>29</strong> fotos · <strong>6</strong> sesiones
            </span>
            <a href="{{ route('cliente.galeria') }}" class="btn btn-outline btn-sm">
                <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                </svg>
                Descargar todo
            </a>
        </div>
    </div>
@endsection

{{-- ══════════════════════════════════════════
     ESTILOS ESPECÍFICOS DEL DASHBOARD
     (complementan mobile.css; específicos de
      esta vista para no inflar el CSS global)
══════════════════════════════════════════ --}}
@push('styles')
    <style>
        /* Identidad de escritorio dentro del hero (oculta en móvil) */
        .dash-hero__identity {
            display: none;
            align-items: center;
            gap: 14px;
        }
        .dash-hero__avatar {
            width: 44px;
            height: 44px;
            border-radius: 50%;
            background: rgba(201,168,76,0.18);
            border: 1.5px solid rgba(201,168,76,0.35);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 15px;
            font-weight: 500;
            color: var(--gold);
            flex-shrink: 0;
        }
        .dash-hero__identity-name {
            font-size: 16px;
            font-weight: 500;
            color: var(--white);
        }
        .dash-hero__identity-meta {
            font-size: 11px;
            color: rgba(255,255,255,0.4);
            margin-top: 2px;
        }

        /* Subtítulo y total del gráfico */
        .dash-chart-subtitle {
            font-size: 11px;
            color: var(--text-3);
            margin-top: 2px;
        }
        .dash-chart-total {
            text-align: right;
        }
        .dash-chart-total__num {
            display: block;
            font-size: 20px;
            font-weight: 500;
            color: var(--navy);
            line-height: 1;
        }
        .dash-chart-total__label {
            font-size: 10px;
            color: var(--text-3);
        }

        @media (min-width: 769px) {
            .dash-hero__greeting,
            .dash-hero__name { display: none; }
            .dash-hero__identity { display: flex; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const meses   = @json($sesionesPorMes['meses']);
        const totales = @json($sesionesPorMes['totales']);

        const style    = getComputedStyle(document.documentElement);
        const accent   = style.getPropertyValue('--gold').trim()      || '#c9a84c';
        const mutedTxt = style.getPropertyValue('--text-3').trim()    || '#9a9488';
        const borderCl = style.getPropertyValue('--border').trim()    || 'rgba(201,168,76,0.15)';

        new Chart(document.getElementById('sesionesChart').getContext('2d'), {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Sesiones',
                    data: totales,
                    borderColor: accent,
                    backgroundColor: 'rgba(201,168,76,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: accent,
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6,
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
                        backgroundColor: '#1a1612',
                        titleColor: '#c9a84c',
                        bodyColor: '#c8bfaf',
                        borderColor: 'rgba(201,168,76,0.2)',
                        borderWidth: 1,
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} sesión${ctx.parsed.y !== 1 ? 'es' : ''}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: borderCl },
                        border: { display: false },
                        ticks: { font: { size: 11, family: 'DM Sans, sans-serif' }, color: mutedTxt }
                    },
                    y: {
                        beginAtZero: true,
                        grid: { color: borderCl },
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
