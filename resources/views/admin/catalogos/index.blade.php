@extends('layouts.admin')
@section('title', 'Catálogos')

@section('topbar-actions')
    <a href="{{ route('admin.catalogos.create') }}" class="btn btn-primary btn-sm">+ Nuevo Catálogo</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Lista de Catálogos</h2>
            <span style="font-size:12px; color:var(--muted);">{{ $catalogos->total() }} catálogos en total</span>
        </div>
        <div class="table-wrap">
            <table>
                <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Paquetes</th>
                    <th>Vigencia</th>
                    <th>Estado</th>
                    <th>Creado</th>
                    <th>Acciones</th>
                </tr>
                </thead>
                <tbody>
                @forelse($catalogos as $catalogo)
                    <tr>
                        <td style="color:var(--muted);">{{ $catalogo->id }}</td>
                        <td>
                            <strong style="color:#1a1a1a;">{{ $catalogo->nombre }}</strong>
                        </td>
                        <td>
                            @if($catalogo->paquetes->count() > 0)
                                <div style="display:flex; flex-wrap:wrap; gap:4px;">
                                    @foreach($catalogo->paquetes->take(3) as $paquete)
                                        <span class="badge badge-foto">{{ Str::limit($paquete->nombre, 20) }}</span>
                                    @endforeach
                                    @if($catalogo->paquetes->count() > 3)
                                        <span class="badge badge-inactive">+{{ $catalogo->paquetes->count() - 3 }} más</span>
                                    @endif
                                </div>
                            @else
                                <span style="font-size:12px; color:var(--muted);">Sin paquetes</span>
                            @endif
                        </td>
                        <td style="font-size:13px;">
                            @if($catalogo->fecha_inicio_vigencia || $catalogo->fecha_fin_vigencia)
                                <span style="color:#555;">
                                {{ $catalogo->fecha_inicio_vigencia?->format('d/m/Y') ?? '—' }}
                                →
                                {{ $catalogo->fecha_fin_vigencia?->format('d/m/Y') ?? '—' }}
                            </span>
                            @else
                                <span style="color:var(--muted);">Sin vigencia</span>
                            @endif
                        </td>
                        <td>
                        <span class="badge {{ $catalogo->activo ? 'badge-active' : 'badge-inactive' }}">
                            {{ $catalogo->activo ? 'Activo' : 'Inactivo' }}
                        </span>
                        </td>
                        <td style="font-size:12px; color:var(--muted);">
                            {{ $catalogo->created_at->format('d/m/Y') }}
                        </td>
                        <td>
                            <div style="display:flex; gap:6px;">
                                <a href="{{ route('admin.catalogos.show', $catalogo) }}" class="btn btn-outline btn-sm">Ver</a>
                                <a href="{{ route('admin.catalogos.edit', $catalogo) }}" class="btn btn-outline btn-sm">Editar</a>
                                <form method="POST" action="{{ route('admin.catalogos.destroy', $catalogo) }}"
                                      onsubmit="return confirm('¿Eliminar el catálogo «{{ $catalogo->nombre }}»?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" style="text-align:center; padding:48px; color:var(--muted);">
                            No hay catálogos registrados.
                            <a href="{{ route('admin.catalogos.create') }}" style="color:var(--gold); margin-left:6px;">Crear el primero</a>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        @if($catalogos->hasPages())
            <div style="padding:16px 24px; border-top:1px solid var(--border);">
                {{ $catalogos->links() }}
            </div>
        @endif
    </div>
@endsection
