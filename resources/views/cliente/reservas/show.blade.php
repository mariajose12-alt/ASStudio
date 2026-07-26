@extends('layouts.cliente')
@section('title', 'Detalle de Reserva')

@section('content')
    <div class="rf-layout">
        <div class="rf-layout__info">
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

            {{-- Aviso puntual — solo para casos terminales o que requieren acción del cliente --}}
            @if($lineaTiempo['especial'])
                @php $esp = $lineaTiempo['especial']; @endphp
                <div class="rf-card rf-estado-card rf-estado-card--{{ match($esp['tipo']) {
                'rechazada', 'cancelada' => 'dang',
                'modificacion' => 'mod',
                default => 'warn',
            } }}">
                    <div class="rf-estado-icon">
                        @if($esp['tipo'] === 'modificacion')
                            <svg width="22" height="22" fill="none" stroke="#854d0e" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                        @else
                            <svg width="22" height="22" fill="none" stroke="#991b1b" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                        @endif
                    </div>
                    <div style="flex:1;">
                        <div class="rf-estado-title">{{ $esp['titulo'] }}</div>
                        @if($esp['descripcion'])
                            <div class="rf-estado-sub">{{ $esp['descripcion'] }}</div>
                        @endif
                        @if($esp['tipo'] === 'modificacion')
                            <button type="button" class="rf-btn-responder" onclick="abrirModal()">Responder sugerencia</button>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <div class="rf-layout__timeline">
            {{-- Timeline de progreso — solo si la reserva sigue su curso normal --}}
            @if(count($lineaTiempo['pasos']) > 0)
                <div class="rf-card" style="padding: 22px 20px 20px;">
                    <div style="font-family:'Playfair Display',serif; font-size:15px; color:var(--navy,#1a2332); margin-bottom:18px;">
                        ¿Dónde está tu reserva?
                    </div>

                    <div class="rf-timeline">
                        @foreach($lineaTiempo['pasos'] as $i => $paso)
                            <div class="rf-timeline-item rf-timeline-item--{{ $paso['estado'] }}">
                                <div class="rf-timeline-marker">
                                    @if($paso['estado'] === 'completado')
                                        <svg width="12" height="12" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                    @elseif($paso['estado'] === 'alerta')
                                        <svg width="10" height="10" fill="none" stroke="#fff" viewBox="0 0 24 24" stroke-width="3" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="8" x2="12" y2="13"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                                    @endif
                                </div>
                                @if($i < count($lineaTiempo['pasos']) - 1)
                                    <div class="rf-timeline-line rf-timeline-line--{{ $paso['estado'] === 'completado' ? 'completado' : 'pendiente' }}"></div>
                                @endif

                                <div class="rf-timeline-body">
                                    <div class="rf-timeline-titulo">{{ $paso['titulo'] }}</div>
                                    <div class="rf-timeline-desc">{{ $paso['descripcion'] }}</div>
                                    @if(!empty($paso['accion']))
                                        <a href="{{ $paso['accion']['url'] }}" class="rf-timeline-accion">{{ $paso['accion']['label'] }} →</a>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>

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

@push('styles')
    <style>
        .rf-timeline { position: relative; }
        .rf-timeline-item {
            position: relative;
            display: flex;
            gap: 14px;
            padding-bottom: 22px;
        }
        .rf-timeline-item:last-child { padding-bottom: 0; }

        .rf-timeline-marker {
            width: 22px; height: 22px;
            border-radius: 50%;
            flex-shrink: 0;
            display: flex; align-items: center; justify-content: center;
            border: 2px solid var(--border, #e5e1d8);
            background: #fff;
            z-index: 1;
        }
        .rf-timeline-item--completado .rf-timeline-marker { background: #16a34a; border-color: #16a34a; }
        .rf-timeline-item--actual .rf-timeline-marker { background: #fff; border-color: var(--accent, #e87722); box-shadow: 0 0 0 4px rgba(232,119,34,0.12); }
        .rf-timeline-item--actual .rf-timeline-marker::after {
            content: ''; width: 8px; height: 8px; border-radius: 50%; background: var(--accent, #e87722);
        }
        .rf-timeline-item--alerta .rf-timeline-marker { background: #dc2626; border-color: #dc2626; }
        .rf-timeline-item--pendiente .rf-timeline-marker { background: #fff; border-color: var(--border, #e5e1d8); }

        .rf-timeline-line {
            position: absolute;
            left: 10px; top: 22px;
            width: 2px;
            height: calc(100% - 22px);
        }
        .rf-timeline-line--completado { background: #16a34a; }
        .rf-timeline-line--pendiente { background: var(--border, #e5e1d8); }

        .rf-timeline-body { flex: 1; padding-top: 1px; }
        .rf-timeline-titulo {
            font-size: 13.5px; font-weight: 600;
            color: var(--navy, #1a2332);
        }
        .rf-timeline-item--pendiente .rf-timeline-titulo,
        .rf-timeline-item--pendiente .rf-timeline-desc { color: #b8b2a5; }
        .rf-timeline-desc {
            font-size: 12.5px; color: var(--muted, #8a8478);
            margin-top: 2px; line-height: 1.5;
        }
        .rf-timeline-accion {
            display: inline-block;
            margin-top: 8px;
            font-size: 12.5px; font-weight: 700;
            color: var(--accent, #e87722);
            text-decoration: none;
        }
        .rf-timeline-accion:hover { text-decoration: underline; }

        .rf-layout { display: flex; flex-direction: column; gap: 14px; }

        @media (min-width: 860px) {
            .rf-layout {
                display: grid;
                grid-template-columns: 1fr 380px;
                align-items: start;
                gap: 20px;
            }
            .rf-layout__timeline { position: sticky; top: 24px; }
        }
    </style>
@endpush

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
