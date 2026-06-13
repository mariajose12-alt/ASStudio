@extends('layouts.cliente')
@section('title', 'Detalle de Reserva')

@section('content')
    <div style="max-width: 660px; margin: 0 auto; padding: 1.25rem 1rem;">

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Tarjeta principal --}}
        <div class="rf-card" style="margin-bottom: 14px;">

            {{-- Hero --}}
            <div class="rf-hero">
                @php
                    $nombre   = $reserva->cliente->usuario->persona->nombre;
                    $apellido = $reserva->cliente->usuario->persona->apellido;
                    $iniciales = strtoupper(mb_substr($nombre, 0, 1) . mb_substr($apellido, 0, 1));

                    $badgeClass = match($reserva->estado) {
                        'PENDIENTE'              => 'rf-badge--warn',
                        'APROBADA'               => 'rf-badge--succ',
                        'RECHAZADA','CANCELADA'  => 'rf-badge--dang',
                        'MODIFICACION_PROPUESTA' => 'rf-badge--mod',
                        default                  => 'rf-badge--sec',
                    };

                    $badgeLabel = match($reserva->estado) {
                        'PENDIENTE'              => 'Pendiente',
                        'APROBADA'               => 'Aprobada',
                        'RECHAZADA'              => 'Rechazada',
                        'CANCELADA'              => 'Cancelada',
                        'MODIFICACION_PROPUESTA' => 'Cambio propuesto',
                        default                  => $reserva->estado,
                    };
                @endphp
                <div class="rf-avatar">{{ $iniciales }}</div>
                <div class="rf-hero__info">
                    <div class="rf-hero__name">{{ $nombre }} {{ $apellido }}</div>
                    <div class="rf-hero__sub">
                    <span>
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->translatedFormat('d \d\e F, Y') }}
                    </span>
                        <span>
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                        {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('H:i') }} hrs
                    </span>
                    </div>
                </div>
                <span class="rf-badge {{ $badgeClass }}">{{ $badgeLabel }}</span>
            </div>

            {{-- Precio --}}
            <div class="rf-precio-strip">
                <div>
                    <div class="rf-strip-lbl">Total</div>
                    <div class="rf-precio-val">RD$ {{ number_format($reserva->precio_total, 2) }}</div>
                </div>
                <div style="text-align: right;">
                    <div class="rf-strip-lbl">Paquete</div>
                    <div class="rf-strip-val">{{ $reserva->paquete->nombre }}</div>
                </div>
            </div>

            {{-- Detalles --}}
            <div class="rf-details">
                <div class="rf-det-row">
                    <div class="rf-det-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    </div>
                    <div class="rf-det-body">
                        <div class="rf-det-lbl">Lugar</div>
                        <div class="rf-det-val">{{ $reserva->lugar ?? 'Estudio' }}</div>
                        <div class="rf-det-sub">Tipo: {{ $reserva->tipo }}</div>
                    </div>
                </div>

                @if($reserva->descripcion)
                    <div class="rf-det-row">
                        <div class="rf-det-icon">
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                        </div>
                        <div class="rf-det-body">
                            <div class="rf-det-lbl">Descripción</div>
                            <div class="rf-det-val">{{ $reserva->descripcion }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Aviso según estado --}}
        @if($reserva->estado === 'APROBADA')
            <div class="rf-card rf-estado-card rf-estado-card--succ">
                <div class="rf-estado-icon">
                    <svg width="22" height="22" fill="none" stroke="#16a34a" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                </div>
                <div>
                    <div class="rf-estado-title">¡Tu reserva fue aprobada!</div>
                    <div class="rf-estado-sub">El fotógrafo confirmó tu sesión. Te esperamos el día acordado.</div>
                </div>
            </div>

        @elseif($reserva->estado === 'RECHAZADA')
            <div class="rf-card rf-estado-card rf-estado-card--dang">
                <div class="rf-estado-icon">
                    <svg width="22" height="22" fill="none" stroke="#991b1b" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                </div>
                <div>
                    <div class="rf-estado-title">Reserva rechazada</div>
                    @if($reserva->motivo_rechazo)
                        <div class="rf-estado-sub">{{ $reserva->motivo_rechazo }}</div>
                    @endif
                </div>
            </div>

        @elseif($reserva->estado === 'MODIFICACION_PROPUESTA')
            <div class="rf-card rf-estado-card rf-estado-card--mod">
                <div class="rf-estado-icon">
                    <svg width="22" height="22" fill="none" stroke="#854d0e" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                </div>
                <div style="flex: 1;">
                    <div class="rf-estado-title">El fotógrafo propone un cambio</div>
                    @if($reserva->motivo_rechazo)
                        <div class="rf-estado-motivo">{{ $reserva->motivo_rechazo }}</div>
                    @endif
                    <button type="button" class="rf-btn-responder" onclick="abrirModal()">
                        Responder sugerencia
                    </button>
                </div>
            </div>

        @elseif($reserva->estado === 'PENDIENTE')
            <div class="rf-card rf-estado-card rf-estado-card--warn">
                <div class="rf-estado-icon">
                    <svg width="22" height="22" fill="none" stroke="#92400e" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                </div>
                <div>
                    <div class="rf-estado-title">Reserva en revisión</div>
                    <div class="rf-estado-sub">El fotógrafo revisará tu solicitud en breve.</div>
                </div>
            </div>
        @endif

        {{-- Modal sugerencia --}}
        <div class="rf-modal-backdrop" id="modalSugerencia">
            <div class="rf-modal">
                <button class="rf-modal-close" onclick="cerrarModal()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 6 6 18M6 6l12 12"/></svg>
                </button>

                <h3 class="rf-modal-title">Sugerencia del fotógrafo</h3>
                <p class="rf-modal-sub">Elige cómo quieres responder:</p>

                <div class="rf-modal-motivo">{{ $reserva->motivo_rechazo }}</div>

                <div class="rf-modal-acciones">

                    {{-- Aceptar --}}
                    <form method="POST" action="{{ route('cliente.reservas.responder-sugerencia', $reserva) }}"
                          onsubmit="this.querySelector('[type=submit]').disabled=true">
                        @csrf @method('PATCH')
                        <input type="hidden" name="accion" value="ACEPTAR">
                        <button type="submit" class="rf-modal-btn rf-modal-btn--succ">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M5 13l4 4L19 7"/></svg>
                            Aceptar sugerencia
                        </button>
                    </form>

                    {{-- Editar y reenviar --}}
                    <form method="POST" action="{{ route('cliente.reservas.responder-sugerencia', $reserva) }}"
                          onsubmit="this.querySelector('#btnConfirmarEditar').disabled=true">
                        @csrf @method('PATCH')
                        <input type="hidden" name="accion" value="EDITAR">
                        <div id="camposEditar" style="display: none; margin-bottom: 10px;">
                            <label class="rf-modal-label">Nueva descripción</label>
                            <textarea name="descripcion"
                                      rows="3"
                                      class="rf-modal-textarea"
                                      placeholder="Describe los cambios que propones..."></textarea>
                        </div>
                        <button type="button" class="rf-modal-btn rf-modal-btn--outline" id="btnEditar"
                                onclick="toggleEditar()">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Editar y reenviar
                        </button>
                        <button type="submit" class="rf-modal-btn rf-modal-btn--primary" id="btnConfirmarEditar"
                                style="display: none; margin-top: 8px;">
                            Confirmar y reenviar
                        </button>
                    </form>

                    {{-- Cancelar reserva --}}
                    <form method="POST" action="{{ route('cliente.reservas.responder-sugerencia', $reserva) }}"
                          onsubmit="if(!confirm('¿Seguro que quieres cancelar esta reserva?')){ event.preventDefault(); return; } this.querySelector('[type=submit]').disabled=true">
                        @csrf @method('PATCH')
                        <input type="hidden" name="accion" value="CANCELAR">
                        <button type="submit" class="rf-modal-btn rf-modal-btn--dang">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Cancelar reserva
                        </button>
                    </form>

                </div>
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        function abrirModal() {
            document.getElementById('modalSugerencia').classList.add('open');
            document.body.style.overflow = 'hidden';
        }

        function cerrarModal() {
            document.getElementById('modalSugerencia').classList.remove('open');
            document.body.style.overflow = '';
        }

        document.getElementById('modalSugerencia').addEventListener('click', function(e) {
            if (e.target === this) cerrarModal();
        });

        document.addEventListener('keydown', e => {
            if (e.key === 'Escape') cerrarModal();
        });

        function toggleEditar() {
            const campos = document.getElementById('camposEditar');
            const btnEditar = document.getElementById('btnEditar');
            const btnConfirmar = document.getElementById('btnConfirmarEditar');
            const mostrar = campos.style.display === 'none';
            campos.style.display      = mostrar ? 'block' : 'none';
            btnConfirmar.style.display = mostrar ? 'flex' : 'none';
            btnEditar.style.display    = mostrar ? 'none' : 'flex';
        }
    </script>
@endpush
