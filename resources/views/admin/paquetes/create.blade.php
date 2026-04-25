@extends('layouts.admin')
@section('title', 'Nuevo Paquete')

@section('topbar-actions')
    <a href="{{ route('admin.paquetes.index') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Registrar Paquete Fotográfico</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.paquetes.store') }}">
                @csrf
                <div class="form-grid">

                    <div class="form-group full">
                        <label for="nombre">Nombre del paquete</label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre') }}"
                               placeholder="Ej: Paquete Premium Bodas"
                               required>
                        @error('nombre')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group full">
                        <label for="descripcion">Descripción</label>
                        <textarea id="descripcion" name="descripcion"
                                  placeholder="Describe qué incluye este paquete...">{{ old('descripcion') }}</textarea>
                        @error('descripcion')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="cantidad_fotos_incluidas">Cantidad de fotos incluidas</label>
                        <input type="number" id="cantidad_fotos_incluidas" name="cantidad_fotos_incluidas"
                               value="{{ old('cantidad_fotos_incluidas') }}"
                               placeholder="Ej: 30" min="1" required>
                        @error('cantidad_fotos_incluidas')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="precio_base">Precio base (RD$)</label>
                        <input type="number" id="precio_base" name="precio_base"
                               value="{{ old('precio_base') }}"
                               placeholder="0.00" step="0.01" min="0" required>
                        @error('precio_base')<span class="error-msg">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label>Estado</label>
                        <div style="display:flex; align-items:center; gap:10px; padding:10px 0;">
                            <label style="display:flex; align-items:center; gap:8px; text-transform:none; letter-spacing:0; font-size:14px; color:#444; cursor:pointer;">
                                <input type="checkbox" name="activo" value="1"
                                       {{ old('activo', '1') ? 'checked' : '' }}
                                       style="width:16px; accent-color:var(--gold);">
                                Paquete activo (visible para reservas)
                            </label>
                        </div>
                    </div>

                </div>

                <div style="border-top:1px solid var(--border); padding-top:24px; margin-top:8px;">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Paquete</button>
                        <a href="{{ route('admin.paquetes.index') }}" class="btn btn-outline">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
