@extends('layouts.reserva')

@section('formulario')
<div class="reserva-logo">AS <span>Studio</span></div>

{{-- Stepper --}}
<div class="stepper">
    <div class="stepper-step">
        <div class="stepper-circle done">✓</div>
        <span class="stepper-label">Sesión</span>
    </div>
    <div class="stepper-line done"></div>
    <div class="stepper-step">
        <div class="stepper-circle done">✓</div>
        <span class="stepper-label">Fecha</span>
    </div>
    <div class="stepper-line done"></div>
    <div class="stepper-step">
        <div class="stepper-circle active">3</div>
        <span class="stepper-label active">Datos</span>
    </div>
    <div class="stepper-line"></div>
    <div class="stepper-step">
        <div class="stepper-circle">4</div>
        <span class="stepper-label">Resumen</span>
    </div>
</div>

<h5>Paso 3 de 4</h5>
<h4>Confirma tus datos</h4>

{{--
    $usuario es el App\Models\Usuario autenticado.
    Los datos personales viven en $usuario->persona (Persona model).
--}}
@php
    $persona = $usuario->persona;
@endphp

<div style="background:rgba(224,123,42,0.06); border:1px solid rgba(224,123,42,0.2); border-radius:8px; padding:14px 16px; margin-bottom:24px;">
    <div style="font-size:12px; color:#c96a1e; font-weight:600; margin-bottom:4px;">Importante</div>
    <p style="font-size:13px; color:#666; margin:0; line-height:1.5;">
        Verifica que tus datos sean correctos. Los utilizaremos para enviarte la confirmación de tu reserva.
    </p>
</div>

<form method="POST" action="{{ route('cliente.reservas.guardarPaso3') }}">
    @csrf

    <div class="form-floating-modern">
        <label>Nombre completo</label>
        {{-- Navega persona->nombre y persona->apellido --}}
        <input type="text" name="nombre"
               value="{{ old('nombre', $persona->nombre . ' ' . $persona->apellido) }}"
               class="{{ $errors->has('nombre') ? 'is-invalid' : '' }}">
        @error('nombre')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-floating-modern">
        <label>Correo electrónico</label>
        {{-- El email vive en usuario, no en persona --}}
        <input type="email" name="correo"
               value="{{ old('correo', $usuario->email) }}"
               class="{{ $errors->has('correo') ? 'is-invalid' : '' }}">
        @error('correo')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="form-floating-modern">
        <label>Teléfono</label>
        {{-- El teléfono vive en persona --}}
        <input type="text" name="telefono"
               value="{{ old('telefono', $persona->telefono) }}"
               placeholder="Ej: 809-000-0000"
               class="{{ $errors->has('telefono') ? 'is-invalid' : '' }}">
        @error('telefono')
        <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
        <a href="{{ route('cliente.reservas.paso2') }}" style="font-size:13px; color:#9a9488; text-decoration:none;">
            ← Volver
        </a>
        <button type="submit" class="btn-reserva">Siguiente →</button>
    </div>
</form>
@endsection
