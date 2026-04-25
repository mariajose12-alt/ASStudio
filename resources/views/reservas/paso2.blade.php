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
            <div class="stepper-circle active">2</div>
            <span class="stepper-label active">Fecha</span>
        </div>
        <div class="stepper-line"></div>
        <div class="stepper-step">
            <div class="stepper-circle">3</div>
            <span class="stepper-label">Datos</span>
        </div>
        <div class="stepper-line"></div>
        <div class="stepper-step">
            <div class="stepper-circle">4</div>
            <span class="stepper-label">Resumen</span>
        </div>
    </div>

    <h5>Paso 2 de 4</h5>
    <h4>Fecha, Hora y Descripción</h4>

    <form method="POST" action="{{ route('cliente.reservas.guardarPaso2') }}">
        @csrf

        <div class="form-floating-modern">
            <label>Fecha de la sesión</label>
            <input type="date" name="fecha"
                   value="{{ old('fecha') }}"
                   min="{{ date('Y-m-d', strtotime('+1 day')) }}"
                   class="{{ $errors->has('fecha') ? 'is-invalid' : '' }}">
            @error('fecha')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating-modern">
            <label>Hora de inicio</label>
            <input type="time" name="hora"
                   value="{{ old('hora') }}"
                   class="{{ $errors->has('hora') ? 'is-invalid' : '' }}">
            @error('hora')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating-modern">
            <label>Descripción de la sesión</label>
            <textarea name="descripcion" rows="4"
                      placeholder="Cuéntanos qué tienes en mente, el ambiente que buscas, ocasión especial..."
                      class="{{ $errors->has('descripcion') ? 'is-invalid' : '' }}">{{ old('descripcion') }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:8px;">
            <a href="{{ route('cliente.reservas.paso1') }}" style="font-size:13px; color:#9a9488; text-decoration:none;">
                ← Volver
            </a>
            <button type="submit" class="btn-reserva">Siguiente →</button>
        </div>
    </form>
@endsection
