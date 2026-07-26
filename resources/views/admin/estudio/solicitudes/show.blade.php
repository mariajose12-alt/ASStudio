@extends('layouts.admin')
@section('title', 'Solicitud #' . $solicitud->id)

@section('topbar-actions')
    <a href="{{ route('admin.estudio.solicitudes.index') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    <div style="display:grid; grid-template-columns:1fr 1fr; gap:24px;">

        <div class="card">
            <div class="card-header"><h2>Detalle de la solicitud</h2></div>
            <div class="card-body">
                <table class="detail-table">
                    <tr><td>Cliente</td><td>{{ $solicitud->nombre }} {{ $solicitud->apellido }}</td></tr>
                    <tr><td>Email</td><td>{{ $solicitud->email }}</td></tr>
                    <tr><td>Teléfono</td><td>{{ $solicitud->telefono }}</td></tr>
                    <tr><td>Fecha</td><td>{{ $solicitud->fecha->format('d/m/Y') }}</td></tr>
                    <tr>
                        <td>Horario</td>
                        <td>{{ \Carbon\Carbon::parse($solicitud->hora_inicio)->format('H:i') }} – {{ \Carbon\Carbon::parse($solicitud->hora_fin)->format('H:i') }}</td>
                    </tr>
                    <tr><td>Finalidad</td><td>{{ ucfirst(str_replace('_', ' ', $solicitud->finalidad)) }}</td></tr>
                    <tr><td>Cantidad de personas</td><td>{{ $solicitud->cantidad_personas }}</td></tr>
                    <tr>
                        <td>Fondo adicional</td>
                        <td>{{ $solicitud->color_fondo_adicional ? $solicitud->color_fondo : 'No' }}</td>
                    </tr>
                    <tr>
                        <td>Iluminación</td>
                        <td>
                            {{ ucfirst(str_replace('_', ' ', $solicitud->iluminacion)) }}
                            @if($solicitud->iluminacion === 'otro' && $solicitud->iluminacion_otro)
                                — {{ $solicitud->iluminacion_otro }}
                            @endif
                        </td>
                    </tr>
                    <tr><td>Invitados / notas</td><td>{{ $solicitud->invitados ?? '—' }}</td></tr>
                    <tr>
                        <td>Estado</td>
                        <td>
                            @php
                                $badges = [
                                    'pendiente'  => 'badge-foto',
                                    'aprobada'   => 'badge-active',
                                    'rechazada'  => 'badge-inactive',
                                    'cancelada'  => 'badge-inactive',
                                ];
                            @endphp
                            <span class="badge {{ $badges[$solicitud->estado] ?? '' }}">
                                {{ ucfirst($solicitud->estado) }}
                            </span>
                        </td>
                    </tr>
                    @if($solicitud->estado !== 'pendiente')
                        <tr><td>Procesada por</td><td>{{ optional($solicitud->aprobadaPor)->email ?? '—' }}</td></tr>
                        <tr><td>Fecha de procesamiento</td><td>{{ optional($solicitud->aprobada_at)->format('d/m/Y H:i') ?? '—' }}</td></tr>
                        <tr><td>Nota</td><td>{{ $solicitud->nota_socio ?? '—' }}</td></tr>
                    @endif
                </table>
            </div>
        </div>

        @if($solicitud->estado === 'pendiente')
            <div class="card">
                <div class="card-header"><h2>Acción</h2></div>
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert-success" style="margin-bottom:16px;">{{ session('success') }}</div>
                    @endif
                    @if(session('error'))
                        <div class="alert-error" style="margin-bottom:16px;">{{ session('error') }}</div>
                    @endif

                    <form method="POST" action="{{ route('admin.estudio.solicitudes.aprobar', $solicitud) }}" style="margin-bottom:20px;">
                        @csrf
                        <label for="nota-aprobar" style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted); margin-bottom:6px;">
                            Nota (opcional)
                        </label>
                        <textarea id="nota-aprobar" name="nota" rows="2" style="width:100%; margin-bottom:12px; padding:10px; border-radius:8px; border:1px solid var(--border);"></textarea>
                        <button type="submit" class="btn" style="background:#10b981; color:#fff; width:100%;">Aprobar solicitud</button>
                    </form>

                    <form method="POST" action="{{ route('admin.estudio.solicitudes.rechazar', $solicitud) }}">
                        @csrf
                        <label for="nota-rechazar" style="display:block; font-size:12px; text-transform:uppercase; letter-spacing:0.5px; color:var(--muted); margin-bottom:6px;">
                            Motivo del rechazo
                        </label>
                        <textarea id="nota-rechazar" name="nota" rows="2" style="width:100%; margin-bottom:12px; padding:10px; border-radius:8px; border:1px solid var(--border);"></textarea>
                        <button type="submit" class="btn btn-outline" style="width:100%; border-color:#ef4444; color:#ef4444;">Rechazar solicitud</button>
                    </form>
                </div>
            </div>
        @endif
    </div>
@endsection
