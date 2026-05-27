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

    {{-- Flatpickr CSS (Esto es para la parte del calendario) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        /* Que Flatpickr herede el estilo de tus inputs */
        .flatpickr-input {
            width: 100%;
            cursor: pointer;
        }
        /* Horas ocupadas en el select */
        option:disabled {
            color: #ccc;
        }
    </style>

    <form method="POST" action="{{ route('cliente.reservas.guardarPaso2') }}"novalidate>
        @csrf

        {{-- Campo fecha — Flatpickr se monta aquí --}}
        <div class="form-floating-modern">
            <label>Fecha de la sesión</label>
            <input type="text"
                   id="fecha-picker"
                   name="fecha"
                   value="{{ old('fecha') }}"
                   placeholder="Selecciona una fecha"
                   autocomplete="off"
                   readonly
                   class="{{ $errors->has('fecha') ? 'is-invalid' : '' }}">
            @error('fecha')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        {{-- Select de hora — se llena dinámicamente --}}
        <div class="form-floating-modern">
            <label>Hora de inicio</label>
            <select id="hora-select"
                    name="hora"
                    class="{{ $errors->has('hora') ? 'is-invalid' : '' }}">
                <option value="">Primero selecciona una fecha</option>
            </select>
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
    {{-- Flatpickr JS --}}
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script src="https://cdn.jsdelivr.net/npm/flatpickr/dist/l10n/es.js"></script>

    <script>
        const HORAS_DISPONIBLES = [
            '08:00', '09:00', '10:00', '11:00',
            '12:00', '13:00', '14:00', '15:00',
            '16:00', '17:00'
        ];

        let horasOcupadasPorFecha = {};
        let fechasCompletas       = [];

        // 1. Cargar disponibilidad desde la BD
        fetch('/disponibilidad/fechas')
            .then(res => res.json())
            .then(data => {
                // data = { ocupadas: [...], habilitadas: [...] }
                data.ocupadas.forEach(item => {
                    if (!horasOcupadasPorFecha[item.fecha]) {
                        horasOcupadasPorFecha[item.fecha] = [];
                    }
                    horasOcupadasPorFecha[item.fecha].push(item.hora);
                });

                flatpickr('#fecha-picker', {
                    locale:        'es',
                    dateFormat:    'Y-m-d',
                    minDate:       new Date().fp_incr(1),
                    enable:        data.habilitadas,
                    disableMobile: true,
                    onChange: function(selectedDates, dateStr) {
                        actualizarHoras(dateStr);
                    }
                });

                const valorPrevio = document.getElementById('fecha-picker').value;
                if (valorPrevio) actualizarHoras(valorPrevio);
            });

        // 3. Llenar el select de horas según la fecha elegida
        function actualizarHoras(fecha) {
            const select   = document.getElementById('hora-select');
            const ocupadas = horasOcupadasPorFecha[fecha] || [];

            select.innerHTML = '';

            const libres = HORAS_DISPONIBLES.filter(h => !ocupadas.includes(h));

            if (libres.length === 0) {
                const opt  = document.createElement('option');
                opt.value  = '';
                opt.text   = 'No hay horas disponibles';
                opt.disabled = true;
                select.appendChild(opt);
                return;
            }

            libres.forEach(hora => {
                const opt  = document.createElement('option');
                opt.value  = hora;
                opt.text   = hora;
                select.appendChild(opt);
            });

            select.options[0].selected = true;
        }
    </script>
@endsection
