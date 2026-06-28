@extends('layouts.admin')
@section('title', 'Resolver Disputa')

@section('topbar-actions')
    <a href="{{ route('admin.nomina') }}" class="btn btn-outline btn-sm">← Volver a Nómina</a>
@endsection

@section('content')
    @php $persona = $detalle->fotografo->empleado->usuario->persona; @endphp

    <div class="card">
        <div class="card-header">
            <h2>{{ $persona->nombre }} {{ $persona->apellido }}</h2>
            <span class="badge" style="background:#fef3c7; color:#92400e;">En disputa</span>
        </div>
        <div class="card-body">
            <table class="detail-table">
                <tr><td>Período</td><td>{{ $detalle->nomina->periodo }}</td></tr>
                <tr><td>Bruto actual</td><td>RD$ {{ number_format($detalle->salario_bruto, 2) }}</td></tr>
                <tr><td>Neto actual</td><td>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</td></tr>
            </table>
        </div>

        <div style="padding:16px 20px; border-top:1px solid var(--border);">
            <strong style="font-size:13px;">Observación del fotógrafo:</strong>
            <p style="margin:8px 0 0; padding:10px 14px; background:#fef3c7; color:#92400e; border-radius:8px; font-size:13px;">
                {{ $detalle->observacion_fotografo }}
            </p>
        </div>
    </div>

    {{-- Tabla editable de participaciones --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h2>Participaciones del período</h2>
            <span style="font-size:12px; color:var(--muted);">Ajusta lo que falte o sobre antes de aceptar</span>
        </div>

        <div class="table-wrap">
            <table class="table">
                <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Rol</th>
                    <th>Precio Reserva</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                @forelse($participacionesActuales as $p)
                    <tr>
                        <td>{{ $p->sesion->fecha_inicio->format('d/m/Y') }}</td>
                        <td><span class="badge {{ $p->rol === 'PRINCIPAL' ? 'badge-foto' : 'badge-admin' }}">{{ $p->rol }}</span></td>
                        <td>RD$ {{ number_format($p->sesion->reserva->precio_total, 2) }}</td>
                        <td>
                            <form method="POST" action="{{ route('admin.nomina.disputa.eliminar', [$detalle, $p]) }}"
                                  onsubmit="return confirm('¿Eliminar esta participación?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-outline btn-sm" style="color:#dc2626;">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align:center; color:var(--muted); padding:24px;">
                            Este fotógrafo no tiene participaciones registradas en este período.
                        </td>
                    </tr>
                @endforelse
                </tbody>

                {{-- Fila para insertar nueva participación --}}
                <tfoot>
                <tr>
                    <td colspan="4" style="padding:16px; border-top:2px solid var(--border);">
                        <form method="POST" action="{{ route('admin.nomina.disputa.agregar', $detalle) }}" style="display:flex; gap:10px; align-items:end; flex-wrap:wrap;">
                            @csrf
                            <div>
                                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:4px;">Sesión</label>
                                <select name="sesion_id" required style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:220px;">
                                    <option value="">Selecciona una sesión</option>
                                    @foreach($sesionesDisponibles as $sesion)
                                        <option value="{{ $sesion->id }}">
                                            {{ $sesion->fecha_inicio->format('d/m/Y') }} — RD$ {{ number_format($sesion->reserva->precio_total, 2) }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label style="display:block; font-size:12px; color:var(--muted); margin-bottom:4px;">Rol</label>
                                <select name="rol" required style="padding:8px 12px; border-radius:8px; border:1px solid var(--border);">
                                    <option value="PRINCIPAL">Principal</option>
                                    <option value="ASISTENTE">Asistente</option>
                                </select>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm">
                                Insertar participación
                            </button>
                        </form>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>
    </div>

    {{-- Acciones finales --}}
    <div class="card" style="margin-top:20px;">
        <div class="card-body" style="display:flex; gap:12px; justify-content:flex-end;">
            <form method="POST" action="{{ route('admin.nomina.disputa.rechazar', $detalle) }}"
                  onsubmit="return confirm('¿Rechazar este ajuste? El cálculo original quedará confirmado sin cambios.')">
                @csrf
                <button type="submit" class="btn btn-outline">Rechazar ajuste</button>
            </form>

            <form method="POST" action="{{ route('admin.nomina.disputa.aceptar', $detalle) }}"
                  onsubmit="return confirm('¿Aceptar y recalcular este detalle con las participaciones actuales?')">
                @csrf
                <button type="submit" class="btn btn-primary">Aceptar y recalcular</button>
            </form>
        </div>
    </div>
@endsection
