@extends('layouts.admin')
@section('title', 'Detalle de Catálogo')

@section('topbar-actions')
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.catalogos.edit', $catalogo) }}" class="btn btn-primary btn-sm">Editar</a>
        <a href="{{ route('admin.catalogos.index') }}" class="btn btn-outline btn-sm">← Volver</a>
    </div>
@endsection

@section('content')
    <div style="display:grid; grid-template-columns:1fr 300px; gap:24px;">

        <div style="display:flex; flex-direction:column; gap:20px;">
            <div class="card">
                <div class="card-header">
                    <h2>{{ $catalogo->nombre }}</h2>
                    <span class="badge {{ $catalogo->activo ? 'badge-active' : 'badge-inactive' }}">
                        {{ $catalogo->activo ? 'Activo' : 'Inactivo' }}
                    </span>
                </div>
                <div class="card-body">
                    <table class="detail-table">
                        <tr><td>ID</td><td>{{ $catalogo->id }}</td></tr>
                        <tr><td>Nombre</td><td>{{ $catalogo->nombre }}</td></tr>
                        <tr>
                            <td>Estado</td>
                            <td>
                                <span class="badge {{ $catalogo->activo ? 'badge-active' : 'badge-inactive' }}">
                                    {{ $catalogo->activo ? 'Activo' : 'Inactivo' }}
                                </span>
                            </td>
                        </tr>
                        <tr><td>Inicio vigencia</td><td>{{ $catalogo->fecha_inicio_vigencia?->format('d/m/Y') ?? '—' }}</td></tr>
                        <tr><td>Fin vigencia</td><td>{{ $catalogo->fecha_fin_vigencia?->format('d/m/Y') ?? '—' }}</td></tr>
                        <tr><td>Creado</td><td>{{ $catalogo->created_at->format('d/m/Y H:i') }}</td></tr>
                        <tr><td>Actualizado</td><td>{{ $catalogo->updated_at->format('d/m/Y H:i') }}</td></tr>
                    </table>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h2>Paquetes en este catálogo</h2>
                    <span style="font-size:12px; color:var(--text-muted);">{{ $catalogo->paquetes->count() }} paquetes</span>
                </div>
                @if($catalogo->paquetes->count() > 0)
                    <div class="table-wrap">
                        <table>
                            <thead>
                            <tr>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Fotos</th>
                                <th>Precio Base</th>
                                <th>Estado</th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($catalogo->paquetes as $paquete)
                                <tr>
                                    <td>
                                        <a href="{{ route('admin.paquetes.show', $paquete) }}"
                                           style="color:var(--gold); text-decoration:none; font-weight:500;">
                                            {{ $paquete->nombre }}
                                        </a>
                                    </td>
                                    <td>
                                            <span class="badge {{ $paquete->tipo === 'ESTUDIO' ? 'badge-foto' : 'badge-admin' }}">
                                                {{ $paquete->tipo }}
                                            </span>
                                    </td>
                                    <td>{{ $paquete->cantidad_fotos_incluidas }}</td>
                                    <td>RD$ {{ number_format($paquete->precio_base, 2) }}</td>
                                    <td>
                                            <span class="badge {{ $paquete->activo ? 'badge-active' : 'badge-inactive' }}">
                                                {{ $paquete->activo ? 'Activo' : 'Inactivo' }}
                                            </span>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="card-body" style="text-align:center; padding:40px; color:var(--text-muted);">
                        Este catálogo no tiene paquetes asignados.
                        <a href="{{ route('admin.catalogos.edit', $catalogo) }}" style="color:var(--gold); margin-left:4px;">Agregar paquetes</a>
                    </div>
                @endif
            </div>
        </div>

        <div style="display:flex; flex-direction:column; gap:16px;">
            <div class="card">
                <div class="card-body" style="text-align:center; padding:28px 20px;">
                    <div style="font-family:'Playfair Display',serif; font-size:48px; color:var(--gold); line-height:1;">
                        {{ $catalogo->paquetes->count() }}
                    </div>
                    <div style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; margin-top:6px;">
                        Paquetes asignados
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header"><h2>Acciones</h2></div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                    <a href="{{ route('admin.catalogos.edit', $catalogo) }}" class="btn btn-primary" style="justify-content:center;">
                        Editar catálogo
                    </a>
                    <form method="POST" action="{{ route('admin.catalogos.destroy', $catalogo) }}"
                          onsubmit="return confirm('¿Eliminar el catálogo «{{ $catalogo->nombre }}»?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;">
                            Eliminar catálogo
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>
@endsection
