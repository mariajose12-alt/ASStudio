@extends('layouts.fotografo')
@section('title', 'Dashboard')

@section('content')

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalSesiones }}</div>
            <div class="stat-label">Sesiones Totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $sesionesPendientes }}</div>
            <div class="stat-label">Sesiones Pendientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value">{{ $fotografo->experiencia_laboral ?? 0 }}</div>
            <div class="stat-label">Años de Experiencia</div>
        </div>
    </div>

    {{-- Certificaciones --}}
    @if($fotografo->certificaciones)
        <div class="card" style="margin-bottom: 24px;">
            <div class="card-header"><h2>Mis Certificaciones</h2></div>
            <div class="card-body" style="display:flex; flex-wrap:wrap; gap:8px;">
                @foreach((array) $fotografo->certificaciones as $cert)
                    <span class="badge badge-foto">{{ $cert }}</span>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Próximas sesiones --}}
    <div class="card">
        <div class="card-header">
            <h2>Próximas Sesiones</h2>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($sesionesProximas as $sesion)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-bottom:1px solid var(--border);">
                    <div>
                        <div style="font-weight:500; font-size:14px;">
                            {{ $sesion->reserva->paquete->nombre ?? 'Sesión fotográfica' }}
                        </div>
                        <div style="font-size:12px; color:var(--muted); margin-top:3px;">
                            {{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d \d\e F, Y — H:i') }}
                        </div>
                        <div style="font-size:12px; color:var(--muted); margin-top:2px;">
                            Rol:
                            <span style="font-weight:600;">
                                {{ $sesion->pivot->rol ?? '—' }}
                            </span>
                        </div>
                    </div>
                    <span class="badge {{ $sesion->pivot->estado_participacion === 'PENDIENTE' ? 'badge-foto' : 'badge-active' }}">
                        {{ $sesion->pivot->estado_participacion ?? '—' }}
                    </span>
                </div>
            @empty
                <div style="padding:32px 24px; text-align:center; color:var(--muted); font-size:14px;">
                    No tienes sesiones próximas asignadas.
                </div>
            @endforelse
        </div>
    </div>

@endsection
