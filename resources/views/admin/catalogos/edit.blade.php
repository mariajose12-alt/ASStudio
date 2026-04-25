@extends('layouts.admin')
@section('title', 'Editar Catálogo')

@section('topbar-actions')
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.catalogos.show', $catalogo) }}" class="btn btn-outline btn-sm">← Volver</a>
    </div>
@endsection

@section('content')
    <form method="POST" action="{{ route('admin.catalogos.update', $catalogo) }}">
        @csrf
        @method('PUT')

        <div style="display:grid; grid-template-columns:1fr 300px; gap:24px;">

            {{-- Campos principales --}}
            <div style="display:flex; flex-direction:column; gap:20px;">

                <div class="card">
                    <div class="card-header"><h2>Información del Catálogo</h2></div>
                    <div class="card-body" style="display:flex; flex-direction:column; gap:16px;">

                        {{-- Nombre --}}
                        <div>
                            <label style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Nombre</label>
                            <input type="text" name="nombre"
                                   value="{{ old('nombre', $catalogo->nombre) }}"
                                   class="form-control @error('nombre') is-invalid @enderror"
                                   style="margin-top:6px;">
                            @error('nombre')
                            <div style="color:red; font-size:12px; margin-top:4px;">{{ $message }}</div>
                            @enderror
                        </div>

                        {{-- Estado --}}
                        <div>
                            <label style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px; display:block; margin-bottom:8px;">Estado</label>
                            <input type="hidden" name="activo" value="0">
                            <label style="display:inline-flex; align-items:center; gap:8px; cursor:pointer;">
                                <input type="checkbox" name="activo" value="1" id="activo"
                                    {{ old('activo', $catalogo->activo) ? 'checked' : '' }}>
                                <span style="font-size:14px;">Activo</span>
                            </label>
                        </div>

                        {{-- Fechas --}}
                        <div style="display:grid; grid-template-columns:1fr 1fr; gap:16px;">
                            <div>
                                <label style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Inicio vigencia</label>
                                <input type="date" name="fecha_inicio_vigencia"
                                       value="{{ old('fecha_inicio_vigencia', $catalogo->fecha_inicio_vigencia?->format('Y-m-d')) }}"
                                       class="form-control"
                                       style="margin-top:6px;">
                                @error('fecha_inicio_vigencia')
                                <div style="color:red; font-size:12px; margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>
                            <div>
                                <label style="font-size:12px; color:var(--text-muted); text-transform:uppercase; letter-spacing:1px;">Fin vigencia</label>
                                <input type="date" name="fecha_fin_vigencia"
                                       value="{{ old('fecha_fin_vigencia', $catalogo->fecha_fin_vigencia?->format('Y-m-d')) }}"
                                       class="form-control"
                                       style="margin-top:6px;">
                                @error('fecha_fin_vigencia')
                                <div style="color:red; font-size:12px; margin-top:4px;">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                    </div>
                </div>

                {{-- Paquetes --}}
                <div class="card">
                    <div class="card-header">
                        <h2>Paquetes del catálogo</h2>
                        <span style="font-size:12px; color:var(--text-muted);">{{ $paquetes->count() }} disponibles</span>
                    </div>

                    @if($paquetes->count() > 0)
                        <div class="table-wrap">
                            <table>
                                <thead>
                                <tr>
                                    <th style="width:40px;"></th>
                                    <th>Nombre</th>
                                    <th>Tipo</th>
                                    <th>Fotos</th>
                                    <th>Precio Total</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($paquetes as $paquete)
                                    <tr>
                                        <td>
                                            <input type="checkbox" name="paquetes[]" value="{{ $paquete->id }}"
                                                {{ $catalogo->paquetes->contains($paquete->id) ? 'checked' : '' }}>
                                        </td>
                                        <td>{{ $paquete->nombre }}</td>
                                        <td>
                                <span class="badge {{ $paquete->tipo === 'ESTUDIO' ? 'badge-foto' : 'badge-admin' }}">
                                    {{ $paquete->tipo }}
                                </span>
                                        </td>
                                        <td>{{ $paquete->cantidad_fotos_incluidas }}</td>
                                        <td>RD$ {{ number_format($paquete->precio_total, 2) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="card-body" style="text-align:center; padding:40px; color:var(--text-muted);">
                            No hay paquetes activos disponibles.
                        </div>
                    @endif
                </div>

            </div>

            {{-- Panel lateral --}}
            <div style="display:flex; flex-direction:column; gap:16px;">

                <div class="card">
                    <div class="card-header"><h2>Acciones</h2></div>
                    <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                        <button type="submit" class="btn btn-primary" style="justify-content:center;">
                            Guardar cambios
                        </button>
                        <a href="{{ route('admin.catalogos.show', $catalogo) }}" class="btn btn-outline" style="justify-content:center;">
                            Cancelar
                        </a>
                    </div>
                </div>

                {{-- Info --}}
                <div class="card">
                    <div class="card-body" style="font-size:12px; color:var(--text-muted); display:flex; flex-direction:column; gap:6px;">
                        <div>ID: {{ $catalogo->id }}</div>
                        <div>Creado: {{ $catalogo->created_at->format('d/m/Y H:i') }}</div>
                        <div>Actualizado: {{ $catalogo->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                </div>

            </div>
        </div>
    </form>
@endsection
