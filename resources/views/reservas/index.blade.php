@extends('layouts.cliente')

@section('title', 'Mis Reservas')

@section('topbar-actions')
    <a href="{{ route('cliente.reservas.paso1') }}" class="btn-nueva-reserva">
        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Nueva Reserva
    </a>
@endsection

@section('content')

    <div class="reservas-wrapper">

        {{-- TABS --}}
        <div class="reservas-tabs">
            <button class="tab active" data-tab="proximas">Próximas</button>
            <button class="tab" data-tab="historial">Historial</button>
            <button class="tab" data-tab="todas">Todas</button>
        </div>

        {{-- PANEL: Próximas --}}
        <div class="tab-panel active" id="panel-proximas">
            @php
                $proximas = $reservas->filter(fn($r) => in_array(strtolower($r->estado), ['pendiente','aprobada','en_sesion']));
            @endphp

            @forelse($proximas as $reserva)
                <x-reserva-card :reserva="$reserva" />
            @empty
                <div class="empty-state">
                    <svg width="48" height="48" fill="none" stroke="currentColor" viewBox="0 0 24 24" opacity=".3">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    <p>No tienes reservas próximas.</p>
                    <a href="{{ route('cliente.reservas.paso1') }}" class="btn-nueva-reserva mt-2">Agendar sesión</a>
                </div>
            @endforelse
        </div>

        {{-- PANEL: Historial --}}
        <div class="tab-panel" id="panel-historial">
            @php
                $historial = $reservas->filter(fn($r) => in_array(strtolower($r->estado), ['finalizada','cancelada']));
            @endphp

            @forelse($historial as $reserva)
                <x-reserva-card :reserva="$reserva" />
            @empty
                <div class="empty-state">
                    <p>Sin historial de reservas aún.</p>
                </div>
            @endforelse
        </div>

        {{-- PANEL: Todas --}}
        <div class="tab-panel" id="panel-todas">
            @forelse($reservas as $reserva)
                <x-reserva-card :reserva="$reserva" />
            @empty
                <div class="empty-state">
                    <p>Aún no tienes ninguna reserva.</p>
                </div>
            @endforelse
        </div>

    </div>

@endsection
@push('scripts')
    <script>
        // ── Tab switching
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
            });
        });
    </script>
@endpush
