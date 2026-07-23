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
    </div>
@endsection
