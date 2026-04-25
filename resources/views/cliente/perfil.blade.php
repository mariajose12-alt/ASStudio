@extends('layouts.cliente')
@section('title', 'Mi Perfil')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
@endpush

@section('content')

    {{-- Card horizontal del perfil --}}
    <div class="perfil-card">
        <div class="perfil-avatar">
            {{ strtoupper(substr($usuario->persona->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->persona->apellido, 0, 1)) }}
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre">
                {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
            </div>
            <div class="perfil-meta">
                @if($usuario->persona->telefono)
                    <div class="perfil-meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $usuario->persona->telefono }}
                    </div>
                @endif
                <div class="perfil-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $usuario->email }}
                </div>
                <div class="perfil-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Cliente desde {{ $usuario->created_at->format('M Y') }}
                </div>
            </div>
            <div class="perfil-badge">
                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Cuenta activa
            </div>
        </div>
    </div>

    {{-- Gráfico de sesiones --}}
    <div class="card" style="margin-bottom:24px;">
        <div class="card-header">
            <h2>Sesiones realizadas</h2>
            <span style="font-size:12px; color:#9a9488;">Últimos 6 meses</span>
        </div>
        <div class="card-body">
            <div class="chart-wrap">
                <canvas id="sesionesChart"></canvas>
            </div>
        </div>
    </div>

    {{-- Editar datos personales --}}
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
                               value="{{ old('nombre', $usuario->persona->nombre) }}" required>
                        @error('nombre')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido"
                               value="{{ old('apellido', $usuario->persona->apellido) }}" required>
                        @error('apellido')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="text" id="telefono" name="telefono"
                           value="{{ old('telefono', $usuario->persona->telefono) }}"
                           placeholder="+1 809 000 0000">
                    @error('telefono')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $usuario->email) }}" required>
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div style="display:flex; gap:12px; margin-top:4px;">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="{{ route('cliente.perfil') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>

@endsection

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        const meses  = @json($sesionesporMes['meses']);
        const totales = @json($sesionesporMes['totales']);

        const ctx = document.getElementById('sesionesChart').getContext('2d');

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: meses,
                datasets: [{
                    label: 'Sesiones',
                    data: totales,
                    borderColor: '#b8922a',
                    backgroundColor: 'rgba(185,146,42,0.08)',
                    borderWidth: 2,
                    pointBackgroundColor: '#b8922a',
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
                        padding: 10,
                        callbacks: {
                            label: ctx => ` ${ctx.parsed.y} sesión${ctx.parsed.y !== 1 ? 'es' : ''}`
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f0ede7' },
                        ticks: { font: { size: 11 }, color: '#9a9488' }
                    },
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1,
                            font: { size: 11 },
                            color: '#9a9488'
                        },
                        grid: { color: '#f0ede7' }
                    }
                }
            }
        });
    </script>
@endpush
