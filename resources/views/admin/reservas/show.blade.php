@extends('layouts.admin')
@section('title', 'Reserva #' . $reserva->id)

@section('topbar-actions')
    <a href="{{ route('admin.reservas.index') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

        {{-- Datos de la reserva --}}
        <div class="card">
            <div class="card-header"><h2>Detalle de la Reserva</h2></div>
            <div class="card-body">
                <table class="detail-table">
                    <tr><td>Cliente</td><td>{{ $reserva->cliente->usuario->persona->nombre ?? '—' }}</td></tr>
                    <tr><td>Email</td><td>{{ $reserva->cliente->usuario->email ?? '—' }}</td></tr>
                    <tr><td>Teléfono</td><td>{{ $reserva->cliente->usuario->persona->telefono ?? '—' }}</td></tr>
                    <tr><td>Paquete</td><td>{{ $reserva->paquete->nombre ?? '—' }}</td></tr>
                    <tr><td>Tipo</td><td>{{ $reserva->tipo }}</td></tr>
                    <tr><td>Lugar</td><td>{{ $reserva->lugar ?? 'No especificado' }}</td></tr>
                    <tr><td>Fecha</td><td>{{ $reserva->fecha_inicio->format('d/m/Y H:i') }}</td></tr>
                    <tr><td>Precio Total</td><td>${{ number_format($reserva->precio_total, 2) }}</td></tr>
                    <tr><td>Observaciones</td><td>{{ $reserva->observaciones ?? '—' }}</td></tr>
                    <tr>
                        <td>Estado</td>
                        <td>
                            @php
                                $badges = [
                                    'PENDIENTE'   => 'badge-foto',
                                    'APROBADA'    => 'badge-active',
                                    'RECHAZADA'   => 'badge-inactive',
                                    'CANCELADA'   => 'badge-inactive',
                                    'PAGO_RECIBIDO' => 'badge-admin',
                                ];
                            @endphp
                            <span class="badge {{ $badges[$reserva->estado] ?? '' }}">
                                {{ $reserva->estado }}
                            </span>
                        </td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Acciones --}}
        <div class="card">
            <div class="card-header"><h2>Cambiar Estado</h2></div>
            <div class="card-body">
                <p style="font-size:13px; color:#888; margin-bottom:20px;">
                    Selecciona el nuevo estado para esta reserva.
                </p>
                <form method="POST" action="{{ route('admin.reservas.estado', $reserva) }}">
                    @csrf
                    @method('PATCH')
                    <div class="form-group" style="margin-bottom:20px;">
                        <label>Nuevo Estado</label>
                        <select name="estado">
                            @foreach(['PENDIENTE','APROBADA','RECHAZADA','CANCELADA','PAGO_RECIBIDO','MODIFICACION_PROPUESTA'] as $estado)
                                <option value="{{ $estado }}" {{ $reserva->estado === $estado ? 'selected' : '' }}>
                                    {{ $estado }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Cambio</button>
                    </div>
                </form>

                {{-- Acciones rápidas --}}
                <div style="margin-top:24px; border-top:1px solid #e8e4dc; padding-top:20px;">
                    <p style="font-size:12px; color:#999; text-transform:uppercase; letter-spacing:1px; margin-bottom:12px;">Acciones Rápidas</p>
                    <div style="display:flex; flex-direction:column; gap:8px;">
                        @if($reserva->estado === 'PENDIENTE')
                            <form method="POST" action="{{ route('admin.reservas.estado', $reserva) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="APROBADA">
                                <button type="submit" class="btn btn-primary" style="width:100%;">✓ Aprobar Reserva</button>
                            </form>
                            <form method="POST" action="{{ route('admin.reservas.estado', $reserva) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="RECHAZADA">
                                <button type="submit" class="btn btn-danger" style="width:100%;">✗ Rechazar Reserva</button>
                            </form>
                        @endif
                        @if(!in_array($reserva->estado, ['CANCELADA', 'RECHAZADA']))
                            <form method="POST" action="{{ route('admin.reservas.estado', $reserva) }}">
                                @csrf @method('PATCH')
                                <input type="hidden" name="estado" value="CANCELADA">
                                <button type="submit" class="btn btn-outline" style="width:100%;">Cancelar Reserva</button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>

    </div>
@endsection
