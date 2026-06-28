@extends('layouts.admin')
@section('title', 'Editar Empleado')

@section('topbar-actions')
    <a href="{{ route('admin.empleados.show', $empleado) }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    @php
        $persona = $empleado->usuario->persona;
        $usuario = $empleado->usuario;
        $fotografo = $empleado->fotografo;
    @endphp

    <div class="card">
        <div class="card-header">
            <h2>Editar — {{ $persona->nombre }} {{ $persona->apellido }}</h2>
            <span class="badge {{ $empleado->rol === 'FOTOGRAFO' ? 'badge-foto' : 'badge-admin' }}">
                {{ $empleado->rol }}
            </span>
        </div>
        <div class="card-body">
            <form method="POST" action="{{ route('admin.empleados.update', $empleado) }}">
                @csrf @method('PUT')

                {{-- Datos personales --}}
                <div style="margin-bottom:24px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:16px;">
                        Datos Personales
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Nombre</label>
                            <input type="text" name="nombre" value="{{ old('nombre', $persona->nombre) }}" required>
                            @error('nombre')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Apellido</label>
                            <input type="text" name="apellido" value="{{ old('apellido', $persona->apellido) }}" required>
                            @error('apellido')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Teléfono</label>
                            <input type="text" name="telefono" value="{{ old('telefono', $persona->telefono) }}">
                            @error('telefono')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Credenciales --}}
                <div style="border-top:1px solid var(--border); padding-top:24px; margin-bottom:24px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:16px;">
                        Credenciales de Acceso
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" name="email" value="{{ old('email', $usuario->email) }}" required>
                            @error('email')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Nueva Contraseña <span style="color:var(--muted); font-weight:400;">(dejar vacío para no cambiar)</span></label>
                            <input type="password" name="password" placeholder="••••••••">
                            @error('password')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                        <div class="form-group">
                            <label>Estado</label>
                            <select name="estado" required>
                                <option value="ACTIVO"   {{ old('estado', $usuario->estado) == 'ACTIVO'   ? 'selected' : '' }}>Activo</option>
                                <option value="INACTIVO" {{ old('estado', $usuario->estado) == 'INACTIVO' ? 'selected' : '' }}>Inactivo</option>
                            </select>
                            @error('estado')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>
                </div>

                {{-- Rol --}}
                <div style="border-top:1px solid var(--border); padding-top:24px; margin-bottom:24px;">
                    <div style="font-size:11px; text-transform:uppercase; letter-spacing:1px; color:var(--muted); margin-bottom:16px;">
                        Rol y Configuración
                    </div>
                    <div class="form-grid">
                        <div class="form-group">
                            <label>Rol</label>
                            <select name="rol" id="rol" required onchange="toggleFotografo(this.value)">
                                <option value="FOTOGRAFO"     {{ old('rol', $empleado->rol) == 'FOTOGRAFO'     ? 'selected' : '' }}>Fotógrafo</option>
                                <option value="ADMINISTRADOR" {{ old('rol', $empleado->rol) == 'ADMINISTRADOR' ? 'selected' : '' }}>Administrador</option>
                            </select>
                            @error('rol')<span class="error-msg">{{ $message }}</span>@enderror
                        </div>
                    </div>

                    {{-- Campos exclusivos de fotógrafo --}}
                    <div id="campos-fotografo" style="display:{{ old('rol', $empleado->rol) == 'FOTOGRAFO' ? 'block' : 'none' }}; margin-top:16px;">
                        <div class="form-grid">
                            <div class="form-group">
                                <label>Años de experiencia</label>
                                <input type="number" name="experiencia_laboral" min="0"
                                       value="{{ old('experiencia_laboral', $fotografo?->experiencia_laboral ?? 0) }}">
                                @error('experiencia_laboral')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                            <div class="form-group full">
                                <label>Certificaciones <span style="color:var(--muted); font-weight:400;">(separadas por coma)</span></label>
                                <input type="text" name="certificaciones"
                                       value="{{ old('certificaciones', is_array($fotografo?->certificaciones) ? implode(', ', $fotografo->certificaciones) : '') }}"
                                       placeholder="Ej: Adobe Lightroom, Sony Certified">
                                @error('certificaciones')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>

                            <div class="form-group full">
                                <label>Salario Base</label>
                                <div style="display:flex; gap:16px; margin-bottom:10px;">
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="radio" name="tipo_salario" value="default"
                                               {{ old('tipo_salario', $fotografo?->salario_base === null ? 'default' : 'personalizado') == 'default' ? 'checked' : '' }}
                                               onchange="toggleSalarioPersonalizado(false)">
                                        Salario base por defecto (RD$ {{ number_format($configDefault, 2) }})
                                    </label>
                                    <label style="display:flex; align-items:center; gap:6px; font-weight:400;">
                                        <input type="radio" name="tipo_salario" value="personalizado"
                                               {{ old('tipo_salario', $fotografo?->salario_base === null ? 'default' : 'personalizado') == 'personalizado' ? 'checked' : '' }}
                                               onchange="toggleSalarioPersonalizado(true)">
                                        Salario personalizado
                                    </label>
                                </div>
                                <input type="number" step="0.01" name="salario_base" id="input-salario-personalizado"
                                       value="{{ old('salario_base', $fotografo?->salario_base) }}"
                                       style="display:{{ $fotografo?->salario_base !== null ? 'block' : 'none' }};"
                                       placeholder="Ej: 25000.00">
                                @error('salario_base')<span class="error-msg">{{ $message }}</span>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <div style="border-top:1px solid var(--border); padding-top:24px;">
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">Actualizar Empleado</button>
                        <a href="{{ route('admin.empleados.show', $empleado) }}" class="btn btn-outline">Cancelar</a>
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

        function toggleSalarioPersonalizado(mostrar) {
            document.getElementById('input-salario-personalizado').style.display = mostrar ? 'block' : 'none';
        }
    </script>
@endpush
