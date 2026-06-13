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
    {{-- Modal sugerencia --}}
    <div id="modal-sugerencia" style="display:none; position:fixed; inset:0; background:rgba(0,0,0,.5); z-index:1000; align-items:center; justify-content:center;">
        <div style="background:#fff; border-radius:16px; padding:32px; max-width:480px; width:90%; position:relative;">

            <h3 style="font-size:18px; font-weight:600; margin-bottom:8px;">Sugerencia de modificación</h3>
            <p style="font-size:13px; color:#888; margin-bottom:16px;">El fotógrafo propone los siguientes cambios:</p>

            <div id="modal-motivo" style="background:#f8f6f0; border-radius:10px; padding:16px; font-size:14px; color:#444; line-height:1.6; margin-bottom:24px;"></div>

            <div style="display:flex; flex-direction:column; gap:10px;">

                {{-- Aceptar --}}
                <form id="form-aceptar" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="accion" value="ACEPTAR">
                    <button type="submit" class="btn btn-primary" style="width:100%; background:#2e7d52; border-color:#2e7d52;">
                        ✓ Aceptar sugerencia
                    </button>
                </form>

                {{-- Editar y reenviar --}}
                <form id="form-editar" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="accion" value="EDITAR">
                    <div id="campos-editar" style="display:none; margin-bottom:12px;">
                        <div class="form-group" style="margin-bottom:10px;">
                            <label style="font-size:12px; font-weight:600;">Nueva descripción</label>
                            <textarea name="descripcion" rows="3" style="width:100%; padding:10px; border:1px solid #ddd; border-radius:8px; font-size:13px;" placeholder="Describe los cambios que quieres..."></textarea>
                        </div>
                    </div>
                    <button type="button" onclick="toggleEditar()" class="btn btn-outline" style="width:100%;" id="btn-editar">
                        ✎ Editar y reenviar
                    </button>
                    <button type="submit" id="btn-confirmar-editar" style="display:none; width:100%; margin-top:8px;" class="btn btn-primary">
                        Confirmar y reenviar
                    </button>
                </form>

                {{-- Cancelar reserva --}}
                <form id="form-cancelar" method="POST">
                    @csrf @method('PATCH')
                    <input type="hidden" name="accion" value="CANCELAR">
                    <button type="submit" class="btn btn-danger" style="width:100%;"
                            onclick="return confirm('¿Seguro que quieres cancelar esta reserva?')">
                        ✗ Cancelar reserva
                    </button>
                </form>

            </div>

            <button onclick="cerrarModal()" style="position:absolute; top:16px; right:16px; background:none; border:none; font-size:20px; cursor:pointer; color:#aaa;">✕</button>
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

        function abrirModalSugerencia(reservaId, motivo) {
            const base = '/cliente/reservas/' + reservaId + '/responder-sugerencia';

            document.getElementById('modal-motivo').textContent = motivo || 'Sin descripción.';
            document.getElementById('form-aceptar').action  = base;
            document.getElementById('form-editar').action   = base;
            document.getElementById('form-cancelar').action = base;

            const modal = document.getElementById('modal-sugerencia');
            modal.style.display = 'flex';
        }

        function cerrarModal() {
            document.getElementById('modal-sugerencia').style.display = 'none';
            // Reset editar
            document.getElementById('campos-editar').style.display = 'none';
            document.getElementById('btn-confirmar-editar').style.display = 'none';
            document.getElementById('btn-editar').style.display = 'inline-flex';
        }

        function toggleEditar() {
            document.getElementById('campos-editar').style.display = 'block';
            document.getElementById('btn-confirmar-editar').style.display = 'block';
            document.getElementById('btn-editar').style.display = 'none';
        }

        // Cerrar al click fuera del modal
        document.getElementById('modal-sugerencia').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });
    </script>
@endpush
