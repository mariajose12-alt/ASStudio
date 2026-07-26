@extends('layouts.reserva')

@section('formulario')
    <div class="reserva-logo">
        <a href="/" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="Abraham Sánchez" height="45">
        </a>
    </div>

    {{-- Stepper --}}
    @include('reservas.partials.stepper', ['step' => 2, 'requiereTelefono' => $requiereTelefono])

    <h5>Paso 2 de 4</h5>
    <h4>Fecha, Hora y Descripción</h4>

    {{-- Flatpickr CSS --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <style>
        .flatpickr-input {
            width: 100%;
            cursor: pointer;
        }
        option:disabled {
            color: #ccc;
        }
    </style>

    <form method="POST" action="{{ route('cliente.reservas.guardarPaso2') }}" novalidate>
        @csrf

        {{-- Campo fecha — Flatpickr se monta aquí --}}
        <div class="form-floating-modern">
            <label>Fecha de la sesión</label>
            @php $p2 = session('reserva.paso2', []); @endphp

            <input type="text"
                   id="fecha-picker"
                   name="fecha"
                   value="{{ old('fecha', $p2['fecha'] ?? '') }}"
                   readonly autocomplete="off">
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
                      class="{{ $errors->has('descripcion') ? 'is-invalid' : '' }}">{{ old('descripcion', $p2['descripcion'] ?? '') }}</textarea>
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
        // Hora guardada en sesión (para repoblar tras validación)
        const horaGuardada = @json(old('hora', $p2['hora'] ?? null));

        // horasPorFecha: { 'YYYY-MM-DD': ['08:00', '09:00', ...], ... }
        // Se llena con la respuesta del backend; el backend ya filtra
        // los slots sin fotógrafos disponibles, así que aquí solo pintamos.
        let horasPorFecha = {};
        const tipo = @json(session('reserva.paso1.tipo') ?? 'EXTERIOR');

        fetch(`/disponibilidad/fechas?tipo=${tipo}`)
            .then(res => res.json())
            .then(data => {
                horasPorFecha = data.horasPorFecha;

                flatpickr('#fecha-picker', {
                    locale:        'es',
                    dateFormat:    'Y-m-d',
                    minDate:       new Date(new Date().setHours(0, 0, 0, 0) + 86400000),
                    enable:        data.habilitadas,
                    disableMobile: true,
                    onChange(_, dateStr) {
                        actualizarHoras(dateStr);
                    },
                });

                // Si ya hay una fecha guardada en sesión, poblar las horas
                const valorPrevio = document.getElementById('fecha-picker').value;
                if (valorPrevio) actualizarHoras(valorPrevio);
            });

        function actualizarHoras(fecha) {
            const select = document.getElementById('hora-select');
            const horas  = horasPorFecha[fecha] ?? [];

            select.innerHTML = '';

            if (horas.length === 0) {
                const opt  = document.createElement('option');
                opt.value  = '';
                opt.text   = 'No hay horas disponibles';
                select.appendChild(opt);
                return;
            }

            horas.forEach(hora => {
                const opt    = document.createElement('option');
                opt.value    = hora;
                opt.text     = a12h(hora);
                opt.selected = hora === horaGuardada;
                select.appendChild(opt);
            });
        }

        function a12h(hora24) {
            const [hStr, mStr] = hora24.split(':');
            let h = parseInt(hStr, 10);
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${mStr} ${ampm}`;
        }
    </script>
@endsection
