@extends('layouts.fotografo')
@section('title', 'Reservas Pendientes')

@section('content')
    @if($reservas->isEmpty())
        <div class="reserva-empty">
            No hay reservas registradas.
        </div>
    @endif

    <div class="reserva-lista">
        @foreach($reservas as $reserva)
            @php
                $badgeClass = match($reserva->estado) {
                    'APROBADA'             => 'badge-active',
                    'RECHAZADA','CANCELADA'=> 'badge-inactive',
                    'PAGO_RECIBIDO'        => 'badge-admin',
                    default                => 'badge-foto',
                };
            @endphp

            <div class="card reserva-card-f">
                <div class="card-body reserva-card-f-body">

                    {{-- Header --}}
                    <div class="reserva-header">
                        <div>
                            <h3 class="reserva-titulo">
                                {{ $reserva->paquete->nombre ?? 'Paquete' }} · {{ $reserva->tipo }}
                            </h3>
                            <span class="badge {{ $badgeClass }}">{{ $reserva->estado }}</span>
                        </div>
                    </div>

                    <hr class="reserva-divider">

                    {{-- Datos principales --}}
                    <div class="reserva-grid-datos">
                        <div class="reserva-dato">
                            <span class="dato-label">Fecha</span>
                            <span class="dato-valor">{{ $reserva->fecha_inicio->format('d \d\e F, Y') }}</span>
                        </div>
                        <div class="reserva-dato">
                            <span class="dato-label">Horario</span>
                            <span class="dato-valor">{{ $reserva->fecha_inicio->format('H:i') }} hrs</span>
                        </div>
                        <div class="reserva-dato">
                            <span class="dato-label">Ubicación</span>
                            <span class="dato-valor">{{ $reserva->lugar ?? 'Estudio' }}</span>
                        </div>
                        <div class="reserva-dato">
                            <span class="dato-label">Precio</span>
                            <span class="dato-valor dato-valor--precio">RD$ {{ number_format($reserva->precio_total, 2) }}</span>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    @if($reserva->descripcion)
                        <div class="reserva-descripcion">
                            <span class="dato-label">Descripción de la sesión</span>
                            <p class="reserva-descripcion-texto">{{ $reserva->descripcion }}</p>
                        </div>
                    @endif

                    {{-- Info del cliente --}}
                    <div class="reserva-cliente">
                        <span class="dato-label">Información del Cliente</span>
                        <div class="reserva-grid-cliente">
                            <div class="reserva-dato">
                                <span class="dato-sublabel">Nombre y Apellido</span>
                                <span class="dato-valor">
                                    {{ $reserva->cliente->usuario->persona->nombre ?? '—' }}
                                    {{ $reserva->cliente->usuario->persona->apellido ?? '' }}
                                </span>
                            </div>
                            <div class="reserva-dato">
                                <span class="dato-sublabel">Correo</span>
                                <span class="dato-valor">{{ $reserva->cliente->usuario->email ?? '—' }}</span>
                            </div>
                            <div class="reserva-dato">
                                <span class="dato-sublabel">Número Telefónico</span>
                                <span class="dato-valor">{{ $reserva->cliente->usuario->persona->telefono ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Panel de aprobación con duración --}}
                    <div id="aprobar-{{ $reserva->id }}" class="reserva-panel" style="display:none;">
                        <span class="dato-label">Confirmar Duración de la Sesión</span>
                        <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}" class="reserva-panel-form">
                            @csrf
                            <input type="hidden" name="accion" value="APROBADA">

                            <div class="form-group" >
                                <label class="panel-label">Duración *</label>
                                <div style="display:flex; align-items:center; gap:2rem;">
                                    <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                                        <input type="radio"
                                               name="duracion_tipo"
                                               value="estandar"
                                               checked
                                               onchange="toggleDuracionCustom({{ $reserva->id }}, false)">
                                        <span>2 horas (estándar)</span>
                                    </label>

                                    <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                                        <input type="radio"
                                               name="duracion_tipo"
                                               value="personalizada"
                                               onchange="toggleDuracionCustom({{ $reserva->id }}, true)">
                                        <span>Personalizada</span>
                                    </label>
                                </div>

                                <div id="duracion-custom-{{ $reserva->id }}" style="display:none; margin-top:.75rem;">
                                    <input type="number" name="duracion_horas" value="2" min="0.5" max="12" step="0.5"
                                           class="panel-textarea" style="width:120px;"
                                           placeholder="ej: 3.5">
                                    <span style="margin-left:.5rem;">horas</span>
                                </div>
                                {{-- Campo oculto para cuando selecciona estándar --}}
                                <input type="hidden" name="duracion_horas_estandar" value="2">
                            </div>

                            <div class="panel-actions">
                                <button type="submit" class="btn btn-primary">✓ Confirmar y Aprobar</button>
                                <button type="button" class="btn btn-outline"
                                        onclick="toggleAprobar({{ $reserva->id }})">Cancelar</button>
                            </div>
                        </form>
                    </div>

                    {{-- Panel proponer cambios --}}
                    {{-- Panel proponer cambios --}}
                    <div id="propuesta-{{ $reserva->id }}" class="reserva-panel" style="display:none;">
                        <span class="dato-label">Proponer Cambios en la Descripción</span>
                        <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}" class="reserva-panel-form">
                            @csrf
                            <input type="hidden" name="accion" value="MODIFICACION_PROPUESTA">

                            <div class="form-group">
                                <label class="panel-label" for="motivo-propuesta-{{ $reserva->id }}">Motivo de la modificación *</label>
                                <textarea name="motivo" id="motivo-propuesta-{{ $reserva->id }}" rows="4" class="panel-textarea"
                                          placeholder="Describe qué cambios propones en la descripción de la sesión..."
                                          required></textarea>
                            </div>

                            <div class="form-group">
                                <div class="panel-chips">
                                    @foreach ([
                                        'No tengo disponibilidad a esa hora, propongo otro horario.',
                                        'Se necesita más tiempo del solicitado para este tipo de sesión.',
                                        'Propongo cambiar el lugar por uno más adecuado.',
                                    ] as $preset)
                                        <button type="button" class="panel-chip" onclick="usarMotivoPreset(this, 'motivo-propuesta-{{ $reserva->id }}')">{{ $preset }}</button>
                                    @endforeach
                                </div>
                            </div>

                            <div class="form-group">
                                <label class="panel-label">Duración estimada (uso interno) *</label>
                                <p style="font-size:12px; color:#9e8c7e; margin:0 0 10px;">
                                    Esta duración se aplicará automáticamente si el cliente acepta tu propuesta. No se le muestra al cliente.
                                </p>
                                <div style="display:flex; align-items:center; gap:2rem;">
                                    <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                                        <input type="radio"
                                               name="duracion_tipo"
                                               value="estandar"
                                               checked
                                               onchange="toggleDuracionCustomProp({{ $reserva->id }}, false)">
                                        <span>2 horas (estándar)</span>
                                    </label>

                                    <label style="display:flex; align-items:center; gap:.4rem; cursor:pointer;">
                                        <input type="radio"
                                               name="duracion_tipo"
                                               value="personalizada"
                                               onchange="toggleDuracionCustomProp({{ $reserva->id }}, true)">
                                        <span>Personalizada</span>
                                    </label>
                                </div>

                                <div id="duracion-custom-prop-{{ $reserva->id }}" style="display:none; margin-top:.75rem;">
                                    <input type="number" name="duracion_horas" value="2" min="0.5" max="12" step="0.5"
                                           class="panel-textarea" style="width:120px;"
                                           placeholder="ej: 3.5">
                                    <span style="margin-left:.5rem;">horas</span>
                                </div>
                                <input type="hidden" name="duracion_horas_estandar" value="2">
                            </div>

                            <div class="form-group">
                                <label class="panel-label">Nueva fecha y hora propuesta (opcional)</label>
                                <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
                                <input type="text" id="fecha-picker-{{ $reserva->id }}" name="nueva_fecha"
                                       class="panel-textarea" style="cursor:pointer;"
                                       placeholder="Selecciona una fecha" readonly autocomplete="off">
                                <select id="hora-select-{{ $reserva->id }}" name="nueva_hora"
                                        class="panel-textarea" style="margin-top:6px;">
                                    <option value="">Primero selecciona una fecha</option>
                                </select>
                            </div>

                            <div class="panel-actions">
                                <button type="submit" class="btn btn-primary">Enviar Propuesta</button>
                                <button type="button" class="btn btn-outline" onclick="togglePropuesta({{ $reserva->id }})">Cancelar</button>
                            </div>
                        </form>
                    </div>

                    {{-- Panel rechazo --}}
                    <div id="rechazo-{{ $reserva->id }}" class="reserva-panel" style="display:none;">
                        <span class="dato-label">Motivo del Rechazo</span>
                        <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}" class="reserva-panel-form">
                            @csrf
                            <input type="hidden" name="accion" value="RECHAZADA">
                            <div class="form-group">
                                <label class="panel-label" for="motivo-rechazo-{{ $reserva->id }}">Indica el motivo *</label>
                                <textarea name="motivo" id="motivo-rechazo-{{ $reserva->id }}" rows="4" class="panel-textarea"
                                          placeholder="Explica por qué no puedes aceptar esta reserva..."
                                          required></textarea>
                            </div>
                            <div class="form-group">
                                <div class="panel-chips">
                                    @foreach ([
                                        'No tengo disponibilidad en la fecha solicitada.',
                                        'El lugar propuesto está fuera de mi zona de cobertura.',
                                        'El tipo de sesión no coincide con mi especialidad.',
                                        'Conflicto con otra reserva ya confirmada.',
                                    ] as $preset)
                                        <button type="button" class="panel-chip" onclick="usarMotivoPreset(this, 'motivo-rechazo-{{ $reserva->id }}')">{{ $preset }}</button>
                                    @endforeach
                                </div>
                            </div>
                            <div class="panel-actions">
                                <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                                <button type="button" class="btn btn-outline" onclick="toggleRechazo({{ $reserva->id }})">Cancelar</button>
                            </div>
                        </form>
                    </div>

                    {{-- Acciones PENDIENTE --}}
                    @if($reserva->estado === 'PENDIENTE')
                        <div id="actions-{{ $reserva->id }}" class="reserva-actions">
                            <button type="button" class="btn btn-aprobar" onclick="toggleAprobar({{ $reserva->id }})">
                                ✓ Aprobar Reserva
                            </button>
                            <button type="button" class="btn btn-sugerir" onclick="togglePropuesta({{ $reserva->id }})">
                                ✎ Sugerir Modificación
                            </button>
                            <button type="button" class="btn btn-rechazar" onclick="toggleRechazo({{ $reserva->id }})">
                                ✗ Rechazar
                            </button>
                        </div>
                    @endif

                    {{-- Acciones APROBADA --}}
                    @if($reserva->estado === 'APROBADA' && $reserva->sesion?->estado !== 'CERRADA')
                        <form method="POST"
                              action="{{ route('fotografo.reservas.accion', $reserva) }}"
                              class="reserva-panel-form"
                              onsubmit="this.querySelector('[type=submit]').disabled = true; this.querySelector('[type=submit]').textContent = 'Procesando...';">
                            @csrf
                            <input type="hidden" name="accion" value="CERRAR_SESION">
                            <button type="submit" class="btn btn-aprobar btn-full"
                                    onclick="if(!confirm('¿Marcar esta sesión como cerrada?')) { event.preventDefault(); return; }">
                                ✓ Cerrar sesión
                            </button>
                        </form>
                    @elseif($reserva->sesion?->estado === 'CERRADA')
                        <span class="reserva-cerrada">✓ Sesión cerrada</span>
                    @endif

                </div>
            </div>
        @endforeach
    </div>

    {{-- Paginación --}}

@endsection

@push('scripts')
    <script>
        function usarMotivoPreset(btn, textareaId) {
            const textarea = document.getElementById(textareaId);
            textarea.value = btn.textContent.trim();
            btn.closest('.panel-chips').querySelectorAll('.panel-chip').forEach(c => c.classList.remove('panel-chip--activo'));
            btn.classList.add('panel-chip--activo');
            textarea.focus();
        }

        function toggleAprobar(id) {
            const panel = document.getElementById('aprobar-' + id);
            const isHidden = panel.style.display === 'none' || panel.style.display === '';
            panel.style.display = isHidden ? 'block' : 'none';
            toggleActionButtons(id, isHidden);
            if (isHidden) panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function toggleDuracionCustom(id, show) {
            document.getElementById('duracion-custom-' + id).style.display = show ? 'block' : 'none';
        }

        function togglePropuesta(id) {
            const panel = document.getElementById('propuesta-' + id);
            const isHidden = panel.style.display === 'none' || panel.style.display === '';
            panel.style.display = isHidden ? 'block' : 'none';
            toggleActionButtons(id, isHidden);
            if (isHidden) {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                inicializarPickerPropuesta(id);
            }
        }

        const fotografoIdActual = {{ auth()->user()->empleado->fotografo->id }};
        const pickersListos = {};
        const horasPorFechaPorReserva = {};
        let flatpickrCargado = false;

        function inicializarPickerPropuesta(reservaId) {
            if (pickersListos[reservaId]) return;
            pickersListos[reservaId] = true;

            const cargarPicker = () => {
                fetch(`/disponibilidad/fotografo/${fotografoIdActual}?excluir_reserva=${reservaId}`)
                    .then(res => res.json())
                    .then(data => {
                        horasPorFechaPorReserva[reservaId] = data.horasPorFecha;

                        flatpickr('#fecha-picker-' + reservaId, {
                            dateFormat: 'Y-m-d',
                            minDate:    new Date(new Date().setHours(0, 0, 0, 0) + 86400000),
                            enable:     data.habilitadas,
                            disableMobile: true,
                            onChange(_, dateStr) {
                                const select = document.getElementById('hora-select-' + reservaId);
                                const horas  = horasPorFechaPorReserva[reservaId][dateStr] ?? [];
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
                            },
                        });
                    });
            };

            if (flatpickrCargado) {
                cargarPicker();
            } else {
                flatpickrCargado = true;
                const script1 = document.createElement('script');
                script1.src = 'https://cdn.jsdelivr.net/npm/flatpickr';
                script1.onload = cargarPicker;
                document.head.appendChild(script1);
            }
        }

        function toggleRechazo(id) {
            const panel = document.getElementById('rechazo-' + id);
            const isHidden = panel.style.display === 'none' || panel.style.display === '';
            panel.style.display = isHidden ? 'block' : 'none';
            toggleActionButtons(id, isHidden);
            if (isHidden) panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function toggleActionButtons(id, disable) {
            const actionGroup = document.getElementById('actions-' + id);
            if (!actionGroup) return;
            actionGroup.querySelectorAll('button, input[type="submit"]').forEach(btn => {
                btn.disabled = disable;
                btn.style.opacity = disable ? '0.4' : '1';
                btn.style.pointerEvents = disable ? 'none' : 'auto';
            });
        }

        function toggleDuracionCustomProp(id, show) {
            document.getElementById('duracion-custom-prop-' + id).style.display = show ? 'block' : 'none';
        }
    </script>
@endpush
