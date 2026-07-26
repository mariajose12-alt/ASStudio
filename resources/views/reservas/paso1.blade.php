@extends('layouts.reserva')

@section('formulario')
    <div class="reserva-logo">
        <a href="/" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Abraham Sánchez" height="45">
        </a>
    </div>

    {{-- Stepper --}}
    @include('reservas.partials.stepper', ['step' => 1, 'requiereTelefono' => $requiereTelefono])

    <h5>Paso 1 de 4</h5>
    <h4>Especificaciones de la Sesión</h4>

    <form method="POST" action="{{ route('cliente.reservas.guardarPaso1') }}" novalidate>
        @csrf

        <div class="form-floating-modern">
            <label>Catálogo</label>
            <select name="catalogo_id" id="catalogo_id" class="@error('catalogo_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach($catalogos as $catalogo)
                    <option value="{{ $catalogo->id }}"
                        {{ (old('catalogo_id', $p1['catalogo_id'] ?? '') == $catalogo->id) ? 'selected' : '' }}>
                        {{ $catalogo->nombre }}
                    </option>
                @endforeach
            </select>
            @error('catalogo_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="form-floating-modern">
            <label>Paquete</label>
            <select name="paquete_id" id="paquete_id" class="@error('paquete_id') is-invalid @enderror">
                <option value="">Seleccione</option>
            </select>
            @error('paquete_id')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="mb-2">
            <label style="font-size:0.75rem; font-weight:600; letter-spacing:0.08em; text-transform:uppercase; color:#999;">Tipo de Sesión</label>
        </div>
        <div class="radio-group">
            <label class="radio-option">
                <input type="radio" name="tipo" value="ESTUDIO" {{ old('tipo', $p1['tipo'] ?? 'ESTUDIO') === 'ESTUDIO' ? 'checked' : '' }}>
                Estudio
            </label>
            <label class="radio-option">
                <input type="radio" name="tipo" value="EXTERIOR" {{ old('tipo', $p1['tipo'] ?? '') === 'EXTERIOR' ? 'checked' : '' }}>
                Exterior
            </label>
        </div>
        @error('tipo')
        <div class="text-danger small mb-3">{{ $message }}</div>
        @enderror

        <div class="form-floating-modern" id="lugar_div" style="display:none;">
            <label>Lugar Específico</label>
            <input type="text" name="lugar" placeholder="Ingresa la dirección" value="{{ old('lugar', $p1['lugar'] ?? '') }}"
                   class="@error('lugar') is-invalid @enderror">
            @error('lugar')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex justify-content-end mt-3">
            <button type="submit" class="btn-reserva">Siguiente →</button>
        </div>
    </form>

@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {

            const lugarDiv = document.getElementById('lugar_div');
            const lugarInput = document.querySelector('input[name="lugar"]');

            // Manejo del cambio de tipo
            document.querySelectorAll('input[name="tipo"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    if (this.value === 'EXTERIOR') {
                        lugarDiv.style.display = 'block';
                        lugarInput.setAttribute('required', 'required');
                    } else {
                        lugarDiv.style.display = 'none';
                        lugarInput.removeAttribute('required');
                        lugarInput.value = '';
                    }
                });
            });

            // Verifica el estado inicial
            const tipoSeleccionado = document.querySelector('input[name="tipo"]:checked');
            if (tipoSeleccionado && tipoSeleccionado.value === 'EXTERIOR') {
                lugarDiv.style.display = 'block';
                lugarInput.setAttribute('required', 'required');
            }

            document.getElementById('catalogo_id').addEventListener('change', function () {
                cargarPaquetes(this.value, null);
            });

            // Al cargar la página, si hay catálogo guardado, carga sus paquetes
            const catalogoGuardado = document.getElementById('catalogo_id').value;
            if (catalogoGuardado) {
                cargarPaquetes(catalogoGuardado, paqueteGuardado ?? @json(old('paquete_id')));
            }
        });

        // El paquete guardado en sesión (null si es primera visita)
        const paqueteGuardado = @json($p1['paquete_id'] ?? null);

        function cargarPaquetes(catalogoId, seleccionar) {
            const paqueteSelect = document.getElementById('paquete_id');
            paqueteSelect.innerHTML = '<option value="">Seleccione</option>';
            if (!catalogoId) return;

            fetch(`/api/paquetes/${catalogoId}`)
                .then(res => res.json())
                .then(paquetes => {
                    paquetes.forEach(p => {
                        const opt = document.createElement('option');
                        opt.value = p.id;
                        opt.text  = `${p.nombre} - $${p.precio_base}`;
                        // Selecciona si coincide con old() o con sesión
                        if (String(p.id) === String(seleccionar)) opt.selected = true;
                        paqueteSelect.appendChild(opt);
                    });
                });
        }
    </script>
@endpush
