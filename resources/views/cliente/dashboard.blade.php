@extends('layouts.cliente')
@section('title', 'Inicio')

@section('topbar-actions')
    <a href="{{ route('cliente.reservas.paso1') }}" class="btn btn-primary">
        + Nueva Reserva
    </a>
@endsection

@section('content')

    <div class="stats-grid">
        <div class="stat-card">
            <div class="stat-value">{{ $totalReservas }}</div>
            <div class="stat-label">Reservas totales</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#b8922a;">{{ $reservasPendientes }}</div>
            <div class="stat-label">Pendientes</div>
        </div>
        <div class="stat-card">
            <div class="stat-value" style="color:#2e7d52;">{{ $reservasCompletadas }}</div>
            <div class="stat-label">Completadas</div>
        </div>
    </div>

    {{-- Próximas reservas --}}
    <div class="card">
        <div class="card-header">
            <h2>Próximas sesiones</h2>
            <a href="{{ route('cliente.reservas.index') }}" class="btn btn-outline" style="font-size:12px; padding:6px 14px;">
                Ver todas
            </a>
        </div>
        <div class="card-body" style="padding:0;">
            @forelse($proximasReservas as $reserva)
                <div style="display:flex; align-items:center; justify-content:space-between; padding:16px 24px; border-bottom:1px solid #f0ede7;">
                    <div>
                        <div style="font-weight:500; font-size:14px;">
                            {{ $reserva->paquete->nombre ?? 'Sesión fotográfica' }}
                        </div>
                        <div style="font-size:12px; color:#9a9488; margin-top:3px;">
                            {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d \d\e F, Y') }}
                        </div>
                    </div>
                    <span class="badge badge-{{ strtolower($reserva->estado) }}">
                        {{ $reserva->estado }}
                    </span>
                </div>
            @empty
                <div style="padding:32px 24px; text-align:center; color:#9a9488; font-size:14px;">
                    No tienes sesiones próximas.
                    <a href="{{ route('cliente.reservas.paso1') }}" style="color:#b8922a; font-weight:500; margin-left:4px;">
                        Reservar ahora →
                    </a>
                </div>
            @endforelse
        </div>
    </div>

    {{-- Accesos rápidos --}}
    <div class="card">
        <div class="card-header"><h2>Explorar</h2></div>
        <div class="card-body" style="display:grid; grid-template-columns: repeat(auto-fit, minmax(200px,1fr)); gap:14px;">
            {{--
            <a href="{{ route('cliente.galeria') }}" class="btn btn-outline" style="justify-content:center; padding:18px; flex-direction:column; gap:8px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                Ver Galería
            </a>
            --}}
            <a href="{{ route('cliente.reservas.paso1') }}" class="btn btn-outline" style="justify-content:center; padding:18px; flex-direction:column; gap:8px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4v16m8-8H4"/></svg>
                Nueva Reserva
            </a>
            <a href="{{ route('cliente.perfil') }}" class="btn btn-outline" style="justify-content:center; padding:18px; flex-direction:column; gap:8px;">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                Mi Perfil
            </a>
        </div>
    </div>

@endsection
