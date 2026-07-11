@extends('layouts.cliente')
@section('title', 'Mi Perfil')

@section('content')

    {{-- ══════════════════════════════════════════
         CARD DE PERFIL — avatar + datos + badge
         Móvil: centrado, apilado
         Desktop: horizontal con chart de actividad
              integrado (ver .perfil-card en mobile.css)
    ══════════════════════════════════════════ --}}
    <div class="perfil-card">
        <div class="perfil-avatar" aria-hidden="true">
            {{ strtoupper(substr($usuario->persona->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->persona->apellido, 0, 1)) }}
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre">
                {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
            </div>
            <div class="perfil-meta">
                @if($usuario->persona->telefono)
                    <div class="perfil-meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $usuario->persona->telefono }}
                    </div>
                @endif
                <div class="perfil-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $usuario->email }}
                </div>
                <div class="perfil-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Cliente desde {{ $usuario->created_at->format('M Y') }}
                </div>
            </div>
            <div class="perfil-badge">
                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Cuenta activa
            </div>
        </div>

        {{-- Mini-chart de actividad — visible solo en desktop dentro de la card --}}
        <div class="perfil-card__chart">
            <span class="perfil-card__chart-label">Actividad · 6 meses</span>
            <div class="chart-wrap chart-wrap--mini">
                <canvas id="sesionesChartAside"
                        role="img"
                        aria-label="Gráfico de sesiones de los últimos 6 meses">
                </canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         RESUMEN RÁPIDO — stats compactos
         Útil para que el usuario vea de un vistazo
         su actividad sin necesidad del gráfico
    ══════════════════════════════════════════ --}}
    <div class="perfil-stats">
        <div class="perfil-stat">
            <div class="perfil-stat__val">{{ $sesionesporMes['totales'] ? array_sum($sesionesporMes['totales']) : 0 }}</div>
            <div class="perfil-stat__label">Sesiones (6m)</div>
        </div>
        <div class="perfil-stat">
            <div class="perfil-stat__val" style="color: var(--green-dark);">
                {{ end($sesionesporMes['totales']) ?? 0 }}
            </div>
            <div class="perfil-stat__label">Este mes</div>
        </div>
        <div class="perfil-stat">
            <div class="perfil-stat__val" style="color: #7d5a00;">
                {{ $usuario->created_at->diffInMonths(now()) }}
            </div>
            <div class="perfil-stat__label">Meses contigo</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         GRÁFICO DE SESIONES (vista completa)
         Móvil: card propia
         Desktop: se oculta — el mini-chart en
         .perfil-card cumple esta función (ver CSS)
    ══════════════════════════════════════════ --}}
    <div class="card perfil-chart-card-full">
        <div class="card-header">
            <h2>Sesiones realizadas</h2>
            <span style="font-size:11px; color:var(--text-3);">Últimos 6 meses</span>
        </div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="sesionesChart"
                        role="img"
                        aria-label="Gráfico de sesiones de los últimos 6 meses">
                </canvas>
            </div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         EDITAR DATOS PERSONALES
         Móvil: inputs full-width, áreas táctiles
                de 48px, dos columnas para
                nombre/apellido
         Desktop: card con max-width (ver mobile.css)
    ══════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header"><h2>Editar datos personales</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('cliente.perfil.update') }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre', $usuario->persona->nombre) }}"
                               autocomplete="given-name" required>
                        @error('nombre')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido"
                               value="{{ old('apellido', $usuario->persona->apellido) }}"
                               autocomplete="family-name" required>
                        @error('apellido')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono"
                           value="{{ old('telefono', $usuario->persona->telefono) }}"
                           autocomplete="tel"
                           placeholder="+1 809 000 0000">
                    @error('telefono')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $usuario->email) }}"
                           autocomplete="email" required>
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Móvil: botones full-width apilados.
                     Desktop: en línea (ver .form-actions abajo) --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="{{ route('cliente.perfil') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    {{-- ══════════════════════════════════════════
         CAMBIAR CONTRASEÑA
    ══════════════════════════════════════════ --}}
    <div class="card" id="cambiar-password">
        <div class="card-header"><h2>Cambiar contraseña</h2></div>
        <div class="card-body">

            @if (session('status') === 'password-updated')
                <div class="alert-success">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Contraseña actualizada correctamente.
                </div>
            @endif

            @if ($errors->updatePassword->any())
                <div class="alert-error">
                    Revisa los campos marcados abajo.
                </div>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label for="current_password">Contraseña actual</label>
                    <input type="password" id="current_password" name="current_password"
                           autocomplete="current-password" required>
                    @error('current_password', 'updatePassword')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password">Nueva contraseña</label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" required>
                    @error('password', 'updatePassword')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar nueva contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           autocomplete="new-password" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Actualizar contraseña</button>
                </div>
            </form>
        </div>
    </div>

@endsection

{{-- ══════════════════════════════════════════
     ESTILOS ESPECÍFICOS DE PERFIL
══════════════════════════════════════════ --}}
@push('styles')
    <style>
        /* Botones del formulario: full-width apilados en móvil */
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 8px;
        }
        .form-actions .btn { width: 100%; justify-content: center; }

        /* Mini-chart dentro de perfil-card — solo desktop */
        .perfil-card__chart { display: none; }
        .perfil-card__chart-label {
            font-size: 11px;
            color: var(--text-3);
            display: block;
            margin-bottom: 6px;
        }
        .chart-wrap--mini { height: 70px; }

        @media (min-width: 769px) {
            /* En desktop: botones en línea, ancho automático */
            .form-actions {
                flex-direction: row;
            }
            .form-actions .btn { width: auto; }

            /* Mostrar mini-chart dentro de la card de perfil */
            .perfil-card__chart {
                display: block;
                flex: 0 0 220px;
                margin-left: auto;
                align-self: stretch;
            }

            /* Ocultar la card de gráfico completa: el mini-chart la sustituye */
            .perfil-chart-card-full { display: none; }
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const meses   = @json($sesionesporMes['meses']);
        const totales = @json($sesionesporMes['totales']);

        const style    = getComputedStyle(document.documentElement);
        const accent   = style.getPropertyValue('--green-dark').trim() || '#2d6a4f';
        const mutedTxt = style.getPropertyValue('--text-3').trim()     || '#9a9488';
        const borderCl = style.getPropertyValue('--border').trim()     || 'rgba(201,168,76,0.15)';

        const chartConfig = (compact) => ({
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Sesiones',
                    data: totales,
                    borderColor: accent,
                    backgroundColor: 'rgba(45,106,79,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: accent,
                    pointRadius: compact ? 2 : 4,
                    pointHoverRadius: compact ? 4 : 6,
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
                        padding: 10,
                        cornerRadius: 8,
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} sesión${ctx.parsed.y !== 1 ? 'es' : ''}`
                        }
                    }
                },
                scales: {
                    x: {
                        display: !compact,
                        grid: { color: borderCl },
                        ticks: { font: { size: 11 }, color: mutedTxt }
                    },
                    y: {
                        display: !compact,
                        beginAtZero: true,
                        ticks: { stepSize: 1, font: { size: 11 }, color: mutedTxt },
                        grid: { color: borderCl }
                    }
                }
            }
        });

        // Gráfico principal (visible en móvil)
        const elFull = document.getElementById('sesionesChart');
        if (elFull) new Chart(elFull.getContext('2d'), chartConfig(false));

        // Mini gráfico dentro de la card de perfil (visible en desktop)
        const elMini = document.getElementById('sesionesChartAside');
        if (elMini) new Chart(elMini.getContext('2d'), chartConfig(true));
    </script>
    <script>
        // Si venimos de un error o éxito relacionado a la contraseña,
        // llevamos al usuario directo a esa card en vez de dejarlo arriba del todo.
        @if ($errors->updatePassword->any() || session('status') === 'password-updated')
        document.getElementById('cambiar-password')?.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        @endif
    </script>
@endpush
