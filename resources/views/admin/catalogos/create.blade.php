@extends('layouts.admin')
@section('title', 'Nuevo Catálogo')

@section('topbar-actions')
    <a href="{{ route('admin.catalogos.index') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Crear Catálogo</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.catalogos.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group full">
                        <label for="nombre">Nombre del catálogo</label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Catálogo Verano 2025"
                               required>
                        @error('nombre')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_inicio_vigencia">Fecha de inicio de vigencia</label>
                        <input type="date" id="fecha_inicio_vigencia" name="fecha_inicio_vigencia"
                               value="{{ old('fecha_inicio_vigencia') }}">
                        @error('fecha_inicio_vigencia')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="fecha_fin_vigencia">Fecha de fin de vigencia</label>
                        <input type="date" id="fecha_fin_vigencia" name="fecha_fin_vigencia"
                               value="{{ old('fecha_fin_vigencia') }}">
                        @error('fecha_fin_vigencia')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <div style="padding:10px 0;">
                            <label style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0; font-size:14px; color:#444; cursor:pointer;">
                                <input type="checkbox" name="activo" value="1"
                                       {{ old('activo', '1') ? 'checked' : '' }}
                                       style="width:16px; accent-color:var(--gold);">
                                Catálogo activo
                            </label>
                        </div>
                    </div>

                </div>

                {{-- Selección de paquetes --}}
                <div style="margin-top:8px;">
                    <div style="margin-bottom:16px;">
                        <label style="font-size:12px; text-transform:uppercase; letter-spacing:1px; color:var(--text-muted);">
                            Paquetes incluidos en este catálogo
                        </label>
                        <p style="font-size:12px; color:var(--text-muted); margin-top:4px;">
                            Selecciona los paquetes fotográficos que forman parte de este catálogo.
                        </p>
                    </div>

                    @if($paquetes->count() > 0)
                        <div class="checkbox-grid">
                            @foreach($paquetes as $paqueteFotografico)
                                <label class="checkbox-item">
                                    <input type="checkbox" name="paquetes[]" value="{{ $paqueteFotografico->id }}"
                                        {{ in_array($paqueteFotografico->id, old('paquetes', [])) ? 'checked' : '' }}>
                                    <div>
                                        <div style="font-size:13px; font-weight:500; color:#1a1a1a;">{{ $paqueteFotografico->nombre }}</div>
                                        <div style="font-size:11px; color:var(--text-muted); margin-top:2px;">
                                            Fotos {{ $paqueteFotografico->cantidad_fotos_incluidas}} · RD$ {{ number_format($paqueteFotografico->precio_base, 2) }}
                                        </div>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                    @else
                        <div style="padding:24px; background:var(--page-bg, #f4f2ee); border-radius:10px; text-align:center; color:var(--text-muted); font-size:13px;">
                            No hay paquetes activos disponibles.
                            <a href="{{ route('admin.paquetes.create') }}" style="color:var(--gold); margin-left:4px;">Crear un paquete</a>
                        </div>
                    @endif
                </div>

                <div style="border-top:1px solid var(--border); padding-top:24px; margin-top:24px;">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Catálogo</button>
                        <a href="{{ route('admin.catalogos.index') }}" class="btn btn-outline">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
