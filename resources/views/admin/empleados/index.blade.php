@extends('layouts.admin')
@section('title', 'Empleados')

@section('content')
    @if(session('nueva_password'))
        <div style="background:#fdf8ee; border:1px solid #e8d9b0; border-radius:12px; padding:20px 24px; margin-bottom:24px; display:flex; align-items:flex-start; gap:16px;">
            <div style="font-size:24px;">🔑</div>
            <div>
                <div style="font-weight:600; color:#1a1a1a; margin-bottom:6px;">Usuario creado exitosamente</div>
                <div style="font-size:13px; color:#555; margin-bottom:10px;">
                    Comparte estas credenciales con el empleado. La contraseña no se podrá ver de nuevo.
                </div>
                <div style="display:flex; flex-direction:column; gap:6px;">
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#9a9488; width:90px;">Email</span>
                        <code style="background:#fff; border:1px solid #e8e4dc; padding:4px 12px; border-radius:6px; font-size:13px; color:#1a1a1a;">
                            {{ session('nuevo_email') }}
                        </code>
                    </div>
                    <div style="display:flex; align-items:center; gap:10px;">
                        <span style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:#9a9488; width:90px;">Contraseña</span>
                        <code style="background:#fff; border:1px solid #e8d9b0; padding:4px 12px; border-radius:6px; font-size:13px; color:#b8922a; font-weight:600; letter-spacing:1px;">
                            {{ session('nueva_password') }}
                        </code>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Lista de Empleados</h2>
            <a href="{{ route('admin.empleados.create') }}" class="btn btn-primary btn-sm">+ Nuevo Empleado</a>
        </div>

        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Rol</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($empleados as $emp)
                    @php
                        $persona = $emp->usuario->persona;
                        $usuario = $emp->usuario;
                    @endphp
                    <tr>
                        <td>{{ $emp->id }}</td>
                        <td>{{ $persona->nombre }} {{ $persona->apellido }}</td>
                        <td>{{ $usuario->email }}</td>
                        <td>
                            <span class="badge {{ $emp->rol === 'FOTOGRAFO' ? 'badge-foto' : 'badge-admin' }}">
                                {{ $emp->rol }}
                            </span>
                        </td>
                        <td>
                            <span class="badge {{ $usuario->estado === 'ACTIVO' ? 'badge-active' : 'badge-inactive' }}">
                                {{ $usuario->estado }}
                            </span>
                        </td>
                        <td style="display:flex; gap:8px;">
                            <a href="{{ route('admin.empleados.show', $emp) }}" class="btn btn-outline btn-sm">Ver</a>
                            <a href="{{ route('admin.empleados.edit', $emp) }}" class="btn btn-outline btn-sm">Editar</a>
                            <form method="POST" action="{{ route('admin.empleados.destroy', $emp) }}"
                                  onsubmit="return confirm('¿Eliminar este empleado?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align:center; color:#555; padding:40px;">
                            No hay empleados registrados.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <div>{{ $empleados->links('pagination::bootstrap-5') }}</div>
    </div>
@endsection
