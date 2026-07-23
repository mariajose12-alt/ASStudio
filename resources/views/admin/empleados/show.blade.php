@extends('layouts.admin')
@section('title', 'Detalle Empleado')

@section('topbar-actions')
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-primary btn-sm">Editar</a>
        <a href="{{ route('admin.empleados.index') }}" class="btn btn-outline btn-sm">← Volver</a>
    </div>
@endsection

@section('content')
    @php
        $persona = $empleado->usuario->persona;
        $usuario = $empleado->usuario;
    @endphp

    <div style="display:grid; grid-template-columns:1fr 300px; gap:24px;">

        <div class="card">
            <div class="card-header">
                <h2>{{ $persona->nombre }} {{ $persona->apellido }}</h2>
                <span class="badge {{ $empleado->rol === 'FOTOGRAFO' ? 'badge-foto' : 'badge-admin' }}">
                    {{ $empleado->rol }}
                </span>
            </div>
            <div class="card-body">
                <table class="detail-table">
                    <tr><td>ID Empleado</td><td>{{ $empleado->id }}</td></tr>
                    <tr><td>Nombre</td><td>{{ $persona->nombre }}</td></tr>
                    <tr><td>Apellido</td><td>{{ $persona->apellido }}</td></tr>
                    <tr><td>Teléfono</td><td>{{ $persona->telefono ?? '—' }}</td></tr>
                    <tr><td>Email</td><td>{{ $usuario->email }}</td></tr>
                    <tr>
                        <td>Estado</td>
                        <td>
                            <span class="badge {{ $usuario->estado === 'ACTIVO' ? 'badge-active' : 'badge-inactive' }}">
                                {{ $usuario->estado }}
                            </span>
                        </td>
                    </tr>
                    <tr>
                        <td>Rol</td>
                        <td>
                            <span class="badge {{ $empleado->rol === 'FOTOGRAFO' ? 'badge-foto' : 'badge-admin' }}">
                                {{ $empleado->rol }}
                            </span>
                        </td>
                    </tr>
                    @if($empleado->rol === 'FOTOGRAFO' && $empleado->fotografo)
                        <tr>
                            <td>Experiencia</td>
                            <td>{{ $empleado->fotografo->experiencia_laboral }} año(s)</td>
                        </tr>
                        <tr>
                            <td>Certificaciones</td>
                            <td>
                                @if($empleado->fotografo->certificaciones)
                                    @foreach((array) $empleado->fotografo->certificaciones as $cert)
                                        <span class="badge badge-foto" style="margin-right:4px;">{{ $cert }}</span>
                                    @endforeach
                                @else
                                    —
                                @endif
                            </td>
                        </tr>
                        <tr>
                            <td>Salario Base</td>
                            <td>RD$ {{ number_format($empleado->fotografo->salario_base ?? $configDefault, 2) }}</td>
                        </tr>
                        <tr>
                            <td>Dependientes adicionales (TSS)</td>
                            <td>{{ $empleado->fotografo->dependientes_adicionales ?? 0 }}</td>
                        </tr>
                    @endif
                    <tr><td>Registrado</td><td>{{ $empleado->created_at->format('d/m/Y H:i') }}</td></tr>
                    <tr><td>Actualizado</td><td>{{ $empleado->updated_at->format('d/m/Y H:i') }}</td></tr>
                </table>
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="card">
                <div class="card-header"><h2>Acciones</h2></div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                    <a href="{{ route('admin.empleados.edit', $empleado) }}" class="btn btn-primary" style="justify-content:center;">
                        Editar empleado
                    </a>
                    <form method="POST" action="{{ route('admin.empleados.destroy', $empleado) }}"
                          onsubmit="return confirm('¿Eliminar a {{ $persona->nombre }} {{ $persona->apellido }}?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;">
                            Eliminar empleado
                        </button>
                    </form>
                </div>
            </div>
        </div>

        @if($empleado->fotografo && $historialCambios->isNotEmpty())
            <div class="card" style="margin-top:20px;">
                <div class="card-header">
                    <h2>Historial de Cambios</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $historialCambios->count() }} registro(s)</span>
                </div>
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Campo</th>
                            <th>Valor anterior</th>
                            <th>Valor nuevo</th>
                            <th>Cambiado por</th>
                            <th>Fecha</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($historialCambios as $cambio)
                            <tr>
                                <td>{{ $cambio->etiquetaCampo() }}</td>
                                <td>{{ $cambio->valor_anterior ?? '—' }}</td>
                                <td>{{ $cambio->valor_nuevo ?? '—' }}</td>
                                <td>
                                    {{ $cambio->cambiadoPor?->empleado?->usuario?->persona?->nombre ?? '—' }}
                                    {{ $cambio->cambiadoPor?->empleado?->usuario?->persona?->apellido ?? '' }}
                                </td>
                                <td>{{ $cambio->created_at->format('d/m/Y H:i') }}</td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif

    </div>
@endsection
