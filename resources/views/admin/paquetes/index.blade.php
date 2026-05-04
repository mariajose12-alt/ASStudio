@extends('layouts.admin')
@section('title', 'Paquetes Fotográficos')

@section('topbar-actions')
    <a href="{{ route('admin.paquetes.create') }}" class="btn btn-primary btn-sm">+ Nuevo Paquete</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Lista de Paquetes Fotográficos</h2>
            <span style="font-size:12px; color:var(--muted);">{{ $paquetes->total() }} paquetes en total</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Precio Base</th>
                    <th>Fotos Incluidas</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($paquetes as $paquete)
                    <tr>
                        <td style="color:var(--muted);">{{ $paquete->id }}</td>
                        <td>
                            <strong style="color:#1a1a1a;">{{ $paquete->nombre }}</strong>
                            @if($paquete->descripcion)
                                <br><span style="font-size:12px; color:var(--muted);">{{ Str::limit($paquete->descripcion, 50) }}</span>
                            @endif
                        </td>
                        <td>RD$ {{ number_format($paquete->precio_base, 2) }}</td>
                        <td style="text-align:center;">{{ $paquete->cantidad_fotos_incluidas }}</td>
                        <td>
                        <span class="badge {{ $paquete->activo ? 'badge-active' : 'badge-inactive' }}">
                            {{ $paquete->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.paquetes.show', $paquete) }}" class="btn btn-outline btn-sm">Ver</a>
                                <a href="{{ route('admin.paquetes.edit', $paquete) }}" class="btn btn-outline btn-sm">Editar</a>
                                <form method="POST" action="{{ route('admin.paquetes.destroy', $paquete) }}"
                                      onsubmit="return confirm('¿Eliminar el paquete «{{ $paquete->nombre }}»?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" style="text-align:center; padding:48px; color:var(--muted);">
                            No hay paquetes registrados.
                            <a href="{{ route('admin.paquetes.create') }}" style="color:var(--gold); margin-left:6px;">Crear el primero</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($paquetes->hasPages())
            <div style="padding:16px 24px; border-top:1px solid var(--border);">
                {{ $paquetes->links() }}
            </div>
        @endif
    </div>
@endsection
