@extends('layouts.admin')
@section('title', 'Nuevo Empleado')

@section('topbar-actions')
    <a href="{{ route('admin.empleados.index') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Registrar Empleado</h2>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empleados.store') }}">
                @csrf

                {{-- Sección: Datos personales --}}
                <div style="margin-bottom:24px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:16px;">
                        Datos Personales
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre') }}" required>
                            @error('nombre')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Apellido</label>
                            <input type="text" name="apellido" value="{{ old('apellido') }}" required>
                            @error('apellido')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono') }}" placeholder="Ej: 809-000-0000">
                            @error('telefono')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: Credenciales --}}
                <div style="border-top:1px solid var(--border); padding-top:24px; margin-bottom:24px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:16px;">
                        Credenciales de Acceso
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email') }}" required>
                            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" required>
                                <option value="ACTIVO"   {{ old('estado', 'ACTIVO') == 'ACTIVO'   ? 'selected' : '' }}>Activo</option>
                                <option value="INACTIVO" {{ old('estado') == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Sección: Rol --}}
                <div style="border-top:1px solid var(--border); padding-top:24px; margin-bottom:24px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:16px;">
                        Rol y Configuración
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Rol</label>
                            <select name="rol" id="rol" required onchange="toggleFotografo(this.value)">
                                <option value="">Seleccionar...</option>
                                <option value="FOTOGRAFO"    {{ old('rol') == 'FOTOGRAFO'    ? 'selected' : '' }}>Fotógrafo</option>
                                <option value="ADMINISTRADOR"{{ old('rol') == 'ADMINISTRADOR'? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('rol')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Campos exclusivos de fotógrafo --}}
                    <div id="campos-fotografo" style="display:{{ old('rol') == 'FOTOGRAFO' ? 'block' : 'none' }}; margin-top:16px;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Años de experiencia</label>
                                <input type="number" name="experiencia_laboral" min="0"
                                       value="{{ old('experiencia_laboral', 0) }}">
                                @error('experiencia_laboral')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group full">
                                <label>Certificaciones <span style="color:var(--muted); font-weight:400;">(separadas por coma)</span></label>
                                <input type="text" name="certificaciones"
                                       value="{{ old('certificaciones') }}"
                                       placeholder="Ej: Adobe Lightroom, Sony Certified, WPPI">
                                @error('certificaciones')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group full">
                                <label>Salario Base</label>
                                <div style="display:flex; gap:16px; margin-bottom:10px;">
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="radio" name="tipo_salario" value="default"
                                               {{ old('tipo_salario', 'default') == 'default' ? 'checked' : '' }}
                                               onchange="toggleSalarioPersonalizado(false)">
                                        Salario base por defecto (RD$ {{ number_format($configDefault, 2) }})
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="radio" name="tipo_salario" value="personalizado"
                                               {{ old('tipo_salario', 'default') == 'personalizado' ? 'checked' : '' }}
                                               onchange="toggleSalarioPersonalizado(true)">
                                        Salario personalizado
                                    </label>
                                </div>
                                <input type="number" step="0.01" name="salario_base" id="input-salario-personalizado"
                                       value="{{ old('salario_base') }}"
                                       style="display:{{ old('tipo_salario') === 'personalizado' ? 'block' : 'none' }};"
                                       placeholder="Ej: 25000.00">
                                @error('salario_base')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group full">
                                <label>Dependientes adicionales (TSS)</label>
                                <input type="number" step="1" min="0" max="20" name="dependientes_adicionales"
                                       value="{{ old('dependientes_adicionales', 0) }}">
                                <small style="color: var(--text-muted, #666); display:block; margin-top:4px;">
                                    Solo dependientes registrados fuera del núcleo familiar directo (ej. padres).
                                    No incluyas cónyuge/concubino ni hijos menores — esos ya están cubiertos sin costo
                                    adicional por el 3.04% de SFS. Cada dependiente adicional genera un descuento
                                    extra fijado por la TSS (Resolución 624-02 CNSS).
                                </small>
                                @error('dependientes_adicionales')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div style="border-top:1px solid var(--border); padding-top:24px;">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Guardar Empleado</button>
                        <a href="{{ route('admin.empleados.index') }}" class="btn btn-outline">Cancelar</a>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    function toggleFotografo(rol) {
        document.getElementById('campos-fotografo').style.display =
            rol === 'FOTOGRAFO' ? 'block' : 'none';
    }

    document.addEventListener('DOMContentLoaded', function () {
        toggleFotografo(document.getElementById('rol').value);
    });

    function toggleSalarioPersonalizado(mostrar) {
        document.getElementById('input-salario-personalizado').style.display = mostrar ? 'block' : 'none';
    }
</script>
@endpush
