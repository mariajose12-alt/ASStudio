@extends('layouts.fotografo')
@section('title', 'Reservas Pendientes')
@section('subtitle', 'Revisa y gestiona las solicitudes de reserva de tus clientes.')

@section('content')
    @if($reservas->isEmpty())
        <div class="card" style="text-align:center; padding:60px; color:var(--muted);">
            No hay reservas registradas.
        </div>
    @endif

    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($reservas as $reserva)
            @php
                $badgeClass = match($reserva->estado) {
                    'APROBADA'    => 'badge-active',
                    'RECHAZADA', 'CANCELADA' => 'badge-inactive',
                    'PAGO_RECIBIDO' => 'badge-admin',
                    default       => 'badge-foto',
                };
            @endphp

            <div class="card" style="border-radius:14px; overflow:visible;">
                <div class="card-body" style="padding:28px;">

                    {{-- Header de la card --}}
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
                        <div>
                            <h3 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:400; margin-bottom:4px;">
                                {{ $reserva->paquete->nombre ?? 'Paquete' }} · {{ $reserva->tipo }}
                            </h3>
                            <span style="font-size:12px; color:var(--muted);">#ID-{{ $reserva->id }}</span>
                        </div>
                        <span class="badge {{ $badgeClass }}" style="font-size:11px; padding:5px 14px;">
                            {{ $reserva->estado }}
                        </span>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--border); margin-bottom:20px;">

                    {{-- Datos principales --}}
                    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px;">
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Fecha</div>
                            <div style="font-size:14px;">{{ $reserva->fecha_inicio->format('d \d\e F, Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Horario</div>
                            <div style="font-size:14px;">{{ $reserva->fecha_inicio->format('H:i') }} hrs</div>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Ubicación</div>
                            <div style="font-size:14px;">{{ $reserva->lugar ?? 'Estudio' }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Precio</div>
                            <div style="font-size:14px; color:var(--navy)">RD$ {{ number_format($reserva->precio_total, 2) }}</div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    @if($reserva->descripcion)
                        <div style="background:var(--cloud); border-radius:10px; padding:16px; margin-bottom:20px;">
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:8px;">Descripción de la sesión</div>
                            <p style="font-size:13px; color:#555; margin:0; line-height:1.6;">{{ $reserva->descripcion }}</p>
                        </div>
                    @endif

                    {{-- Info del cliente --}}
                    <div style="margin-bottom:20px;">
                        <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:12px;">Información del Cliente</div>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Nombre y Apellido</div>
                                <div style="font-size:14px; margin-top:2px;">
                                    {{ $reserva->cliente->usuario->persona->nombre ?? '—' }} {{ $reserva->cliente->usuario->persona->apellido ?? '' }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Correo</div>
                                <div style="font-size:14px; margin-top:2px;">{{ $reserva->cliente->usuario->email ?? '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Número Telefónico</div>
                                <div style="font-size:14px; margin-top:2px;">{{ $reserva->cliente->usuario->persona->telefono ?? '—' }}</div>
                            </div>
                        </div>
                    </div>

                    {{-- Panel de proponer cambios (oculto por defecto) --}}
                    <div id="propuesta-{{ $reserva->id }}" style="display:none; border-top:1px solid var(--border); padding-top:20px; margin-top:4px; margin-bottom:20px;">
                        <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:12px;">Proponer Cambios en la Descripción</div>
                        <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}">
                            @csrf
                            <input type="hidden" name="accion" value="MODIFICACION_PROPUESTA">
                            <div class="form-group" style="margin-bottom:16px;">
                                <label style="display:block; margin-bottom:8px; font-weight:500;">Motivo de la modificación *</label>
                                <textarea
                                    name="motivo"
                                    rows="4"
                                    placeholder="Describe qué cambios propones en la descripción de la sesión..."
                                    style="width:100%; padding:10px; border:1px solid var(--border); border-radius:6px; font-size:14px;"
                                    required></textarea>
                            </div>
                            <div style="display:flex; gap:10px;">
                                <button type="submit" class="btn btn-primary">Enviar Propuesta</button>
                                <button type="button" class="btn btn-outline"
                                        onclick="document.getElementById('propuesta-{{ $reserva->id }}').style.display='none'">
                                    Cancelar
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- Acciones --}}
                    @if($reserva->estado === 'PENDIENTE')
                        <div style="display:grid; grid-template-columns:1fr 1fr 1fr; gap:10px;">
                            {{-- Aprobar --}}
                            <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}">
                                @csrf
                                <input type="hidden" name="accion" value="APROBADA">
                                <button type="submit" class="btn btn-primary" style="width:100%; justify-content:center; background:#2e7d52; border-color:#2e7d52;">
                                    ✓ Aprobar Reserva
                                </button>
                            </form>

                            {{-- Sugerir Modificación --}}
                            <button type="button" class="btn btn-primary"
                                    style="width:100%; justify-content:center; background:var(--blue-mid); border-color:var(--blue-mid);"
                                    onclick="togglePropuesta({{ $reserva->id }})">
                                ✎ Sugerir Modificación
                            </button>

                            {{-- Rechazar --}}
                            <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}" id="formRechazar{{ $reserva->id }}">
                                @csrf
                                <input type="hidden" name="accion" value="RECHAZADA">
                                <input type="hidden" name="motivo" value="">
                                <button type="button" class="btn btn-danger" style="width:100%; justify-content:center;"
                                        onclick="rechazarConMotivo({{ $reserva->id }})">
                                    ✗ Rechazar
                                </button>
                            </form>
                        </div>
                    @endif
                    {{-- Acciones Despues de Aprobada y Realizada--}}
                    @if($reserva->estado === 'APROBADA' && $reserva->sesion?->estado !== 'CERRADA')
                        <form method="POST" action="{{ route('fotografo.reservas.accion', $reserva) }}">
                            @csrf
                            <input type="hidden" name="accion" value="CERRAR_SESION">
                            <button type="submit" class="btn btn-primary"
                                    style="width:100%; justify-content:center; background:#2e7d52; border-color:#2e7d52;"
                                    onclick="return confirm('¿Marcar esta sesión como cerrada?')">
                                ✓ Cerrar sesión
                            </button>
                        </form>
                    @elseif($reserva->sesion?->estado === 'CERRADA')
                        <span style="font-size:13px; color:#2e7d52; display:flex; justify-content: center">✓ Sesión cerrada</span>
                    @endif

                </div>
            </div>
        @endforeach
    </div>

    {{-- Agregar Paginacion Aqui --}}

@endsection

@push('scripts')
    <script>
        function togglePropuesta(id) {
            const panel = document.getElementById('propuesta-' + id);
            const isHidden = panel.style.display === 'none' || panel.style.display === '';
            panel.style.display = isHidden ? 'block' : 'none';

            // Scroll suave hacia el panel cuando se abre
            if (isHidden) {
                panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
            }
        }

        function rechazarConMotivo(reservaId) {
            const motivo = prompt('Por favor, indica el motivo del rechazo:');

            if (motivo === null) {
                // Usuario canceló
                return;
            }

            if (motivo.trim() === '') {
                alert('Debes proporcionar un motivo para rechazar la reserva.');
                return;
            }

            // Setear el motivo en el formulario
            const form = document.getElementById('formRechazar' + reservaId);
            form.querySelector('input[name="motivo"]').value = motivo.trim();

            // Enviar el formulario
            form.submit();
        }
    </script>
@endpush
