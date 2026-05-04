@extends('layouts.fotografo')
@section('title', 'Detalle de Reserva')

@section('content')
    <div class="container py-4" style="max-width:700px;">

        @if(session('error'))
            <div class="alert alert-danger">{{ session('error') }}</div>
        @endif

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        {{-- Info de la reserva --}}
        <div class="card border-0 shadow-sm mb-4">
            <div class="card-body">
                <table class="table table-borderless mb-0">
                    <tr>
                        <td class="text-muted" width="140">Cliente</td>
                        <td class="fw-bold">
                            {{ $reserva->cliente->usuario->persona->nombre }}
                            {{ $reserva->cliente->usuario->persona->apellido }}
                        </td>
                    </tr>
                    <tr>
                        <td class="text-muted">Email</td>
                        <td>{{ $reserva->cliente->usuario->email }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Teléfono</td>
                        <td>{{ $reserva->cliente->usuario->persona->telefono ?? '—' }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Paquete</td>
                        <td>{{ $reserva->paquete->nombre }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Fecha</td>
                        <td>{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Tipo</td>
                        <td>{{ $reserva->tipo }}</td>
                    </tr>
                    @if($reserva->lugar)
                        <tr>
                            <td class="text-muted">Lugar</td>
                            <td>{{ $reserva->lugar }}</td>
                        </tr>
                    @endif
                    <tr>
                        <td class="text-muted">Descripción</td>
                        <td>{{ $reserva->descripcion }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Precio</td>
                        <td>RD$ {{ number_format($reserva->precio_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td class="text-muted">Estado</td>
                        <td>
                            @php
                                $badgeClass = match($reserva->estado) {
                                    'PENDIENTE' => 'bg-warning',
                                    'APROBADA' => 'bg-success',
                                    'RECHAZADA' => 'bg-danger',
                                    'CANCELADA' => 'bg-secondary',
                                    default => 'bg-info',
                                };
                            @endphp
                            <span class="badge {{ $badgeClass }}">{{ $reserva->estado }}</span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        @if($reserva->estado === 'PENDIENTE')

            {{-- Formulario de acción --}}
            <div class="card border-0 shadow-sm">
                <div class="card-body">
                    <h5 class="fw-bold mb-3" style="padding-bottom:10px">Gestión de Reserva</h5>

                    <form method="POST"
                          action="{{ route('fotografo.reservas.accion', $reserva) }}"
                          id="formAccion">
                        @csrf

                        {{-- Selector de acción --}}
                        <div class="mb-3">
                            <div class="d-flex gap-2">
                                <button type="button"
                                        class="btn btn-success btn-accion"
                                        data-accion="APROBADA"
                                        style="justify-content:center; background:#2e7d52; border-color:#2e7d52;">
                                    ✓ Aprobar
                                </button>
                                <button type="button"
                                        class="btn btn-warning btn-accion"
                                        data-accion="MODIFICACION_PROPUESTA"
                                        style="justify-content:center; background:var(--blue-mid); border-color:var(--blue-mid);">
                                    ✎ Proponer cambio
                                </button>
                                <button type="button"
                                        class="btn btn-danger btn-accion"
                                        data-accion="RECHAZADA"
                                        style="justify-content:center;">
                                    ✗ Rechazar
                                </button>
                            </div>
                            <input type="hidden" name="accion" id="accionInput">
                        </div>

                        {{-- Motivo (aparece si rechaza o modifica) --}}
                        <div id="campoMotivo" class="mb-3" style="display:none;">
                            <label class="form-label">Motivo / Observación *</label>
                            <textarea name="motivo"
                                      rows="4"
                                      class="form-control"
                                      placeholder="Explica el motivo de tu decisión o los cambios que propones en la descripción..."></textarea>
                        </div>

                        <div id="contenedorEnviar" style="display:none; padding-top:10px">
                            <button type="submit" class="btn btn-dark w-100">
                                Confirmar y notificar al cliente
                            </button>
                        </div>

                    </form>
                </div>
            </div>

        @else
            <div class="alert alert-secondary">
                Esta reserva ya fue procesada — estado actual:
                <strong>{{ $reserva->estado }}</strong>
            </div>
        @endif

    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.btn-accion').forEach(btn => {
            btn.addEventListener('click', function () {
                const accion = this.dataset.accion;

                // Marcar botón activo
                document.querySelectorAll('.btn-accion').forEach(b => b.classList.remove('active'));
                this.classList.add('active');

                // Setear el input hidden
                document.getElementById('accionInput').value = accion;

                // Mostrar/ocultar campos según acción
                const motivo  = document.getElementById('campoMotivo');
                const enviar  = document.getElementById('contenedorEnviar');

                // Mostrar motivo para RECHAZAR y MODIFICAR
                motivo.style.display  = (accion === 'RECHAZADA' || accion === 'MODIFICACION_PROPUESTA') ? 'block' : 'none';

                // Hacer el campo requerido si es necesario
                const motivoTextarea = motivo.querySelector('textarea');
                if (accion === 'RECHAZADA' || accion === 'MODIFICACION_PROPUESTA') {
                    motivoTextarea.setAttribute('required', 'required');
                } else {
                    motivoTextarea.removeAttribute('required');
                }

                // Siempre mostrar botón de enviar
                enviar.style.display  = 'block';
            });
        });
    </script>
@endpush
