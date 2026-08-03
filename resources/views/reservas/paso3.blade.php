@extends('layouts.reserva')

@section('formulario')
    <div class="reserva-logo">
        <a href="/" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Abraham Sánchez" height="45">
        </a>
    </div>

    {{-- Stepper --}}
    @include('reservas.partials.stepper', ['step' => 3, 'requiereTelefono' => $requiereTelefono])

    <h5>Paso 3 de 4</h5>
    <h4>Confirma tus datos</h4>

    {{--
        $usuario es el App\Models\Usuario autenticado.
        Los datos personales viven en $usuario->persona (Persona model).
    --}}
    @php
        $p3     = session('reserva.paso3', []);
        $persona = $usuario->persona;
    @endphp

    <div style="background:rgba(224,123,42,0.06); border:1px solid rgba(224,123,42,0.2); border-radius:8px; padding:14px 16px; margin-bottom:24px;">
        <div style="font-size:12px; color:#c96a1e; font-weight:600; margin-bottom:4px;">Importante</div>
        <p style="font-size:13px; color:#666; margin:0; line-height:1.5;">
            Verifica que tus datos sean correctos. Los utilizaremos para enviarte la confirmación de tu reserva.
        </p>
    </div>

    <form method="POST" action="{{ route('cliente.reservas.guardarPaso3') }}" novalidate>
        @csrf

        <div class="form-floating-modern">
            <label>Nombre completo</label>
            <div class="dato-no-editable">{{ $persona->nombre . ' ' . $persona->apellido }}</div>
        </div>

        <div class="form-floating-modern">
            <label>Correo electrónico</label>
            <div class="dato-no-editable">{{ $usuario->email }}</div>
        </div>

        <div class="form-floating-modern">
            <label>Teléfono</label>
            {{-- El teléfono vive en persona --}}
            <input type="text" name="telefono"
                   class="{{ $errors->has('telefono') ? 'is-invalid' : ''}}"
                   value="{{ old('telefono', $p3['telefono'] ?? $persona->telefono) }}">
            @error('telefono')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small style="display:block; font-size:12px; color:#9a9488; margin-top:6px; line-height:1.4;">
                Lo usamos para notificarte por WhatsApp sobre tu sesión. Es opcional.
            </small>
        </div>
        <div class="form-check" style="margin-top: 12px;">
            <input type="checkbox" id="acepta_whatsapp" name="acepta_whatsapp" value="1"
                {{ old('acepta_whatsapp', $p3['acepta_whatsapp'] ?? $persona->acepta_whatsapp) ? 'checked' : '' }}>
            <label for="acepta_whatsapp" style="font-size:13px; color:var(--ink); margin-left:6px;">
                Acepto recibir notificaciones sobre mi reserva por WhatsApp.
            </label>
        </div>

        <div style="display:flex; justify-content:space-between; align-items:center; margin-top:16px;">
            <a href="{{ route('cliente.reservas.paso2') }}" style="font-size:13px; color:#9a9488; text-decoration:none;">
                ← Volver
            </a>
            <button type="submit" class="btn-reserva">Siguiente →</button>
        </div>
    </form>
@endsection
