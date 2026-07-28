@extends('layouts.fotografo')
@section('title', 'Detalle de Reserva')

@section('content')
    <div class="rf-pg">

        @if(session('error'))
            <div class="rf-alert rf-alert--danger">{{ session('error') }}</div>
        @endif
        @if(session('success'))
            <div class="rf-alert rf-alert--success">{{ session('success') }}</div>
        @endif

        {{-- Tarjeta principal --}}
        <div class="rf-card">

            {{-- Hero cliente --}}
            <div class="rf-hero">
                @php
                    $nombre   = $reserva->cliente->usuario->persona->nombre;
                    $apellido = $reserva->cliente->usuario->persona->apellido;
                    $iniciales = strtoupper(mb_substr($nombre, 0, 1) . mb_substr($apellido, 0, 1));
                @endphp
                <div class="rf-avatar">{{ $iniciales }}</div>
                <div class="rf-hero__info">
                    <div class="rf-hero__name">{{ $nombre }} {{ $apellido }}</div>
                    <div class="rf-hero__sub">
                    <span>
                        <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                        {{ $reserva->cliente->usuario->email }}
                    </span>
                        @if($reserva->cliente->usuario->persona->telefono)
                            <span>
                            <svg width="13" height="13" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 16.92v3a2 2 0 01-2.18 2 19.79 19.79 0 01-8.63-3.07A19.5 19.5 0 013.07 9.8 19.79 19.79 0 01.22 1.18 2 2 0 012.22 0h3a2 2 0 012 1.72c.127.96.361 1.903.7 2.81a2 2 0 01-.45 2.11L6.91 7.91a16 16 0 006.18 6.18l1.27-1.27a2 2 0 012.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0122 16.92z"/></svg>
                            {{ $reserva->cliente->usuario->persona->telefono }}
                        </span>
                        @endif
                    </div>
                </div>
                @php
                    $badgeClass = match($reserva->estado) {
                        'PENDIENTE'  => 'rf-badge--warn',
                        'APROBADA'   => 'rf-badge--succ',
                        'RECHAZADA'  => 'rf-badge--dang',
                        'CANCELADA'  => 'rf-badge--sec',
                        default      => 'rf-badge--sec',
                    };
                @endphp
                <span class="rf-badge {{ $badgeClass }}">{{ $reserva->estado }}</span>
            </div>

            {{-- Franja de precio --}}
            <div class="rf-precio-strip">
                <div>
                    <div class="rf-strip-lbl">Total a cobrar</div>
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
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                    </div>
                    <div class="rf-det-body">
                        <div class="rf-det-lbl">Fecha y horario</div>
                        <div class="rf-det-val">
                            {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->translatedFormat('d \d\e F, Y') }}
                            — {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('H:i') }} hrs
                        </div>
                    </div>
                </div>

                <div class="rf-det-row">
                    <div class="rf-det-icon">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0118 0z"/><circle cx="12" cy="10" r="3"/></svg>
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
                            <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/><polyline points="10 9 9 9 8 9"/></svg>
                        </div>
                        <div class="rf-det-body">
                            <div class="rf-det-lbl">Descripción</div>
                            <div class="rf-det-val">{{ $reserva->descripcion }}</div>
                        </div>
                    </div>
                @endif
            </div>
        </div>

        {{-- Gestión --}}
        @if($reserva->estado === 'PENDIENTE')
            <div class="rf-card" id="rfActCard">
                <div class="rf-act-header">
                    <span class="rf-act-title">Gestionar reserva</span>
                </div>

                <form method="POST"
                      action="{{ route('fotografo.reservas.accion', $reserva) }}"
                      id="formAccion">
                    @csrf
                    <input type="hidden" name="accion" id="accionInput">

                    <div class="rf-act-btns">
                        <button type="button" class="rf-abtn" id="btnAp" data-accion="APROBADA">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Aprobar
                        </button>
                        <button type="button" class="rf-abtn" id="btnMo" data-accion="MODIFICACION_PROPUESTA">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
                            Proponer cambio
                        </button>
                        <button type="button" class="rf-abtn" id="btnRe" data-accion="RECHAZADA">
                            <svg width="20" height="20" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>
                            Rechazar
                        </button>
                    </div>

                    <div class="rf-motivo-wrap" id="campoMotivo" style="display: none;">
                        <label class="rf-motivo-lbl" for="motivoTextarea">Motivo / observación *</label>
                        <textarea id="motivoTextarea"
                                  name="motivo"
                                  rows="4"
                                  class="rf-motivo-ta"
                                  placeholder="Explica el motivo o los cambios que propones..."></textarea>
                    </div>

                    <div class="rf-motivo-wrap" id="campoDuracion" style="display: none; flex-direction: column; gap: .75rem;">
                        <label class="rf-motivo-lbl">Duración de la sesión (uso interno) *</label>
                        <p style="font-size:12px; color:#9e8c7e; margin:0;">No se le muestra al cliente. Si propones un cambio, esta duración se aplicará automáticamente cuando el cliente acepte.</p>
                        <div style="display:flex; align-items:center; gap:2rem;">
                            <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                                <input type="radio" name="duracion_tipo" value="estandar" checked
                                       onchange="toggleDuracionCustomShow(false)">
                                <span>2 horas (estándar)</span>
                            </label>
                            <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                                <input type="radio" name="duracion_tipo" value="personalizada"
                                       onchange="toggleDuracionCustomShow(true)">
                                <span>Personalizada</span>
                            </label>
                        </div>
                        <div id="duracionCustomShow" style="display:none;">
                            <input type="number" name="duracion_horas" value="2" min="0.5" max="12" step="0.5"
                                   class="rf-motivo-ta" style="width:120px; padding:8px;"
                                   placeholder="ej: 3.5">
                            <span style="margin-left:.5rem;">horas</span>
                        </div>
                        <input type="hidden" name="duracion_horas_estandar" value="2">
                    </div>

                    <div class="rf-motivo-wrap" id="campoFecha" style="display: none; flex-direction: column; gap: .75rem;">
                        <label class="rf-motivo-lbl">Nueva fecha y hora propuesta *</label>
                        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                        <input type="text" id="fecha-picker-fot" name="nueva_fecha"
                               class="rf-motivo-ta" style="cursor:pointer;"
                               placeholder="Selecciona una fecha" readonly autocomplete="off">
                        <select id="hora-select-fot" name="nueva_hora" class="rf-motivo-ta">
                            <option value="">Primero selecciona una fecha</option>
                        </select>
                    </div>

                    <div id="contenedorEnviar" style="display: none; padding: 0 20px 20px;">
                        <button type="submit" class="rf-btn-submit">
                            <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                            Confirmar y notificar al cliente
                        </button>
                    </div>
                </form>
            </div>
        @else
            <div class="rf-card">
                <div class="rf-processed">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    Esta reserva ya fue procesada — estado actual:
                    <strong>{{ $reserva->estado }}</strong>
                </div>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        const clases = {
            APROBADA:               'a-ap',
            MODIFICACION_PROPUESTA: 'a-mo',
            RECHAZADA:              'a-re',
        };

        document.querySelectorAll('.rf-abtn').forEach(btn => {
            btn.addEventListener('click', function () {
                const accion = this.dataset.accion;

                document.querySelectorAll('.rf-abtn').forEach(b => {
                    b.className = 'rf-abtn';
                });
                this.classList.add(clases[accion]);

                document.getElementById('accionInput').value = accion;

                const needsMotivo = accion === 'RECHAZADA' || accion === 'MODIFICACION_PROPUESTA';
                const motivoWrap  = document.getElementById('campoMotivo');
                motivoWrap.style.display = needsMotivo ? 'flex' : 'none';

                const ta = motivoWrap.querySelector('textarea');
                needsMotivo ? ta.setAttribute('required', 'required') : ta.removeAttribute('required');

                const needsDuracion = accion === 'APROBADA' || accion === 'MODIFICACION_PROPUESTA';
                document.getElementById('campoDuracion').style.display = needsDuracion ? 'flex' : 'none';

                const needsFecha = accion === 'MODIFICACION_PROPUESTA';
                document.getElementById('campoFecha').style.display = needsFecha ? 'flex' : 'none';
                if (needsFecha) inicializarPickerFotografo();

                document.getElementById('contenedorEnviar').style.display = 'block';
            });
        });

        function toggleDuracionCustomShow(show) {
            document.getElementById('duracionCustomShow').style.display = show ? 'block' : 'none';
        }

        let pickerFotInicializado = false;
        let horasPorFechaFot = {};

        function inicializarPickerFotografo() {
            if (pickerFotInicializado) return;
            pickerFotInicializado = true;

            const fotografoId = {{ auth()->user()->empleado->fotografo->id }};
            const reservaId   = {{ $reserva->id }};

            const script1 = document.createElement('script');
            script1.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
            script1.onload = () => {
                fetch(`/disponibilidad/fotografo/${fotografoId}?excluir_reserva=${reservaId}`)
                    .then(res => res.json())
                    .then(data => {
                        horasPorFechaFot = data.horasPorFecha;

                        flatpickr('#fecha-picker-fot', {
                            dateFormat: 'Y-m-d',
                            minDate:    new Date(new Date().setHours(0, 0, 0, 0) + 86400000),
                            enable:     data.habilitadas,
                            disableMobile: true,
                            onChange(_, dateStr) {
                                actualizarHorasFot(dateStr);
                            },
                        });
                    });
            };
            document.head.appendChild(script1);
        }

        function actualizarHorasFot(fecha) {
            const select = document.getElementById('hora-select-fot');
            const horas  = horasPorFechaFot[fecha] ?? [];

            select.innerHTML = '';

            if (horas.length === 0) {
                const opt = document.createElement('option');
                opt.value = '';
                opt.text  = 'No hay horas disponibles ese día';
                select.appendChild(opt);
                return;
            }

            horas.forEach(hora => {
                const opt = document.createElement('option');
                opt.value = hora;
                opt.text  = hora;
                select.appendChild(opt);
            });
        }
    </script>
@endpush
