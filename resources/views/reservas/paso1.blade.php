@extends('layouts.reserva')

@section('formulario')
    <div class="reserva-logo">AS <span>Studio</span></div>

    {{-- Stepper --}}
    <div class="stepper">
        <div class="stepper-step">
            <div class="stepper-circle active">1</div>
            <span class="stepper-label active">Sesión</span>
        </div>
        <div class="stepper-line"></div>
        <div class="stepper-step">
            <div class="stepper-circle">2</div>
            <span class="stepper-label">Fecha</span>
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

    <h5>Paso 1 de 4</h5>
    <h4>Especificaciones de la Sesión</h4>

    <form method="POST" action="{{ route('cliente.reservas.guardarPaso1') }}">
        @csrf

        <div class="form-floating-modern">
            <label>Catálogo</label>
            <select name="catalogo_id" id="catalogo_id" class="@error('catalogo_id') is-invalid @enderror">
                <option value="">Seleccione</option>
                @foreach($catalogos as $catalogo)
                    <option value="{{ $catalogo->id }}" {{ old('catalogo_id') == $catalogo->id ? 'selected' : '' }}>
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
                <input type="radio" name="tipo" value="ESTUDIO" {{ old('tipo', 'ESTUDIO') == 'ESTUDIO' ? 'checked' : '' }}>
                Estudio
            </label>
            <label class="radio-option">
                <input type="radio" name="tipo" value="EXTERIOR" {{ old('tipo') == 'EXTERIOR' ? 'checked' : '' }}>
                Exterior
            </label>
        </div>
        @error('tipo')
        <div class="text-danger small mb-3">{{ $message }}</div>
        @enderror

        <div class="form-floating-modern" id="lugar_div" style="display:none;">
            <label>Lugar Específico</label>
            <input type="text" name="lugar" placeholder="Ingresa la dirección" value="{{ old('lugar') }}"
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

            document.querySelectorAll('input[name="tipo"]').forEach(radio => {
                radio.addEventListener('change', function () {
                    document.getElementById('lugar_div').style.display =
                        this.value === 'EXTERIOR' ? 'block' : 'none';
                });
            });

            const exteriorRadio = document.getElementById('exterior');
            if (exteriorRadio && exteriorRadio.checked) {
                document.getElementById('lugar_div').style.display = 'block';
            }

            document.getElementById('catalogo_id').addEventListener('change', function () {
                const catalogoId = this.value;
                const paqueteSelect = document.getElementById('paquete_id');
                paqueteSelect.innerHTML = '<option value="">Seleccione</option>';
                if (!catalogoId) return;
                fetch(`/api/paquetes/${catalogoId}`)
                    .then(res => res.json())
                    .then(paquetes => {
                        paquetes.forEach(p => {
                            paqueteSelect.innerHTML += `<option value="${p.id}">${p.nombre} - $${p.precio_base}</option>`;
                        });
                    });
            });

        });
    </script>
@endpush
