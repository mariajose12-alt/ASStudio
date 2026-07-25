@extends('layouts.fotografo')
@section('title', 'Sesiones')

@section('content')

    {{-- Pestañas --}}
    <div class="reservas-tabs">
        <button class="tab active" onclick="switchTab('confirmadas', this)">
            Confirmadas
            @if($confirmadas->count() > 0)
                <span class="tab-badge">{{ $confirmadas->count() }}</span>
            @endif
        </button>
        <button class="tab" onclick="switchTab('en-proceso', this)">
            En Proceso
            @if($enProceso->count() > 0)
                <span class="tab-badge">{{ $enProceso->count() }}</span>
            @endif
        </button>
        <button class="tab" onclick="switchTab('en-edicion', this)">
            En Edición
            @if($enEdicion->count() > 0)
                <span class="tab-badge">{{ $enEdicion->count() }}</span>
            @endif
        </button>
        <button class="tab" onclick="switchTab('ayudantes', this)">
            Ayudantes
            @php $totalAyudantes = $solicitudesPropias->count() + $solicitudesDisponibles->count(); @endphp
            @if($totalAyudantes > 0)
                <span class="tab-badge">{{ $totalAyudantes }}</span>
            @endif
        </button>
    </div>

    {{-- Confirmadas --}}
    <div id="tab-confirmadas" class="tab-content active">
        <div class="sesiones-grid">
            @forelse($confirmadas as $sesion)
                @include('fotografo.sesion.card', ['sesion' => $sesion])
            @empty
                @include('fotografo.sesion.empty', ['mensaje' => 'No tienes sesiones confirmadas.'])
            @endforelse
        </div>
    </div>

    {{-- En Proceso --}}
    <div id="tab-en-proceso" class="tab-content">
        <div class="sesiones-grid">
            @forelse($enProceso as $sesion)
                @include('fotografo.sesion.card', ['sesion' => $sesion])
            @empty
                @include('fotografo.sesion.empty', ['mensaje' => 'No tienes sesiones en proceso.'])
            @endforelse
        </div>
    </div>

    {{-- En Edición --}}
    <div id="tab-en-edicion" class="tab-content">
        <div class="sesiones-grid">
            @forelse($enEdicion as $sesion)
                @include('fotografo.sesion.card', ['sesion' => $sesion])
            @empty
                @include('fotografo.sesion.empty', ['mensaje' => 'No tienes sesiones en edición.'])
            @endforelse
        </div>
    </div>

    {{-- Ayudantes --}}
    <div id="tab-ayudantes" class="tab-content">

        @if($solicitudesPropias->count() > 0)
            <h3 class="ayudante-subtitulo">Tus solicitudes</h3>
            <div class="sesiones-grid">
                @foreach($solicitudesPropias as $solicitud)
                    @include('fotografo.solicitud-ayudante.propia', ['solicitud' => $solicitud])
                @endforeach
            </div>
        @endif

        @if($solicitudesDisponibles->count() > 0)
            <h3 class="ayudante-subtitulo">Solicitudes disponibles</h3>
            <div class="sesiones-grid">
                @foreach($solicitudesDisponibles as $solicitud)
                    @include('fotografo.solicitud-ayudante.disponible', ['solicitud' => $solicitud])
                @endforeach
            </div>
        @endif

        @if($solicitudesPropias->count() === 0 && $solicitudesDisponibles->count() === 0)
            @include('fotografo.sesion.empty', ['mensaje' => 'No hay solicitudes de ayudante por ahora.'])
        @endif
    </div>

    {{-- Modal: fotos seleccionadas por el cliente --}}
    <div class="seleccion-backdrop" id="seleccionModal">
        <div class="seleccion-modal">
            <div class="seleccion-modal-header">
                <div>
                    <h3 class="seleccion-modal-title">Fotos seleccionadas</h3>
                    <p class="seleccion-modal-sub" id="seleccionSub"></p>
                </div>
                <button type="button" class="seleccion-close" onclick="cerrarSeleccion()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <ul class="seleccion-list" id="seleccionList"></ul>
        </div>
    </div>

    {{-- Modal: solicitar ayudantes --}}
    <div class="seleccion-backdrop" id="ayudanteModal">
        <div class="seleccion-modal">
            <div class="seleccion-modal-header">
                <div>
                    <h3 class="seleccion-modal-title">Solicitar ayudantes</h3>
                    <p class="seleccion-modal-sub" id="ayudanteSub"></p>
                </div>
                <button type="button" class="seleccion-close" onclick="cerrarSolicitudAyudante()">
                    <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                    </svg>
                </button>
            </div>
            <form id="ayudanteForm" method="POST" class="ayudante-form">
                @csrf
                <div class="ayudante-campo">
                    <label for="cantidad_ayudantes">¿Cuántos ayudantes necesitas?</label>
                    <input type="number" name="cantidad_ayudantes" id="cantidad_ayudantes" min="1" max="10" value="1" required>
                </div>
                <div class="ayudante-campo">
                    <label for="mensaje">Nota para los fotógrafos (opcional)</label>
                    <textarea name="mensaje" id="mensaje" rows="3" placeholder="Ej. Necesito ayuda con la iluminación..."></textarea>
                </div>
                <button type="submit" class="btn-iniciar">Enviar solicitud</button>
            </form>
        </div>
    </div>

    @push('styles')
        <style>
            .tab {
                display: flex;
                align-items: center;
                gap: 8px;
                padding: 10px 20px;
                font-size: 14px;
                font-weight: 500;
                color: var(--muted);
                background: none;
                border: none;
                border-bottom: 2px solid transparent;
                margin-bottom: -2px;
                cursor: pointer;
                transition: color 0.15s, border-color 0.15s;
            }

            .tab:hover {
                color: var(--navy);
            }

            .tab-badge {
                background: #E8A020;
                color: #fff;
                font-size: 11px;
                font-weight: 700;
                padding: 1px 7px;
                border-radius: 20px;
            }

            .tab-content {
                display: none;
            }

            .tab-content.active {
                display: block;
            }

            .sesiones-grid {
                display: flex;
                flex-direction: column;
                gap: 16px;
            }

            .sesion-card {
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 24px;
                transition: box-shadow 0.2s;
            }

            .sesion-card:hover {
                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.07);
            }

            .sesion-card-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                margin-bottom: 20px;
                gap: 12px;
            }

            .sesion-tipo {
                font-family: 'Playfair Display', serif;
                font-size: 17px;
                font-weight: 600;
                color: var(--navy);
                margin: 0 0 4px 0;
            }

            .sesion-id {
                font-size: 12px;
                color: var(--muted);
            }

            .badge-estado {
                font-size: 11px;
                font-weight: 600;
                padding: 4px 12px;
                border-radius: 20px;
                white-space: nowrap;
                letter-spacing: 0.04em;
            }

            .badge-estado.en_proceso {
                background: #fef3c7;
                color: #92400e;
            }

            .badge-estado.en_edicion {
                background: #ede9fe;
                color: #5b21b6;
            }

            .badge-estado.confirmada {
                background: #dbeafe;
                color: #1e40af;
            }

            .sesion-meta {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 16px;
                margin-bottom: 20px;
                padding-bottom: 20px;
                border-bottom: 1px solid var(--border);
            }

            @media (max-width: 640px) {
                .sesion-meta {
                    grid-template-columns: repeat(2, 1fr);
                }
            }

            .meta-label {
                display: block;
                font-size: 10px;
                font-weight: 600;
                letter-spacing: 0.08em;
                color: var(--muted);
                margin-bottom: 4px;
            }

            .meta-value {
                font-size: 13px;
                color: var(--navy);
                font-weight: 500;
            }

            .sesion-card-footer {
                display: flex;
                align-items: center;
                justify-content: space-between;
                gap: 12px;
            }

            .fotos-badge {
                font-size: 12px;
                color: var(--muted);
                background: var(--blue-mid);
                padding: 4px 10px;
                border-radius: 6px;
                border: 1px solid var(--border);
            }

            .footer-actions {
                display: flex;
                gap: 8px;
                align-items: center;
            }

            .btn-subir {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                background: #E8A020;
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                padding: 9px 18px;
                border-radius: 8px;
                text-decoration: none;
                transition: background 0.15s;
                border: none;
                cursor: pointer;
            }

            .btn-subir:hover {
                background: #c98b18;
            }

            .btn-iniciar {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                background: var(--navy, #1a2340);
                color: #fff;
                font-size: 13px;
                font-weight: 600;
                padding: 9px 10px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                transition: opacity 0.15s;
            }

            .ayudante-form .btn-iniciar {
                width: fit-content;
                align-self: center;
                justify-content: center;
                padding: 10px 22px;
                font-size: 13px;
                font-family: 'DM Sans', sans-serif;
            }

            .btn-iniciar:hover {
                opacity: 0.85;
            }

            .btn-seleccion {
                display: inline-flex;
                align-items: center;
                gap: 6px;
                background: var(--white);
                color: var(--navy);
                font-size: 13px;
                font-weight: 600;
                padding: 8px 14px;
                border-radius: 8px;
                border: 1.5px solid var(--border);
                cursor: pointer;
                transition: background 0.15s, border-color 0.15s;
            }
            .btn-seleccion:hover {
                background: #f5f0ff;
                border-color: #c4b5fd;
            }
            .btn-seleccion-badge {
                background: #ede9fe;
                color: #5b21b6;
                font-size: 11px;
                font-weight: 700;
                padding: 1px 7px;
                border-radius: 20px;
            }

            /* ── Modal selección ── */
            .seleccion-backdrop {
                display: none;
                position: fixed;
                inset: 0;
                background: rgba(0,0,0,0.4);
                z-index: 200;
                align-items: center;
                justify-content: center;
                backdrop-filter: blur(4px);
            }
            .seleccion-backdrop.open { display: flex; }

            .seleccion-modal {
                background: var(--white);
                border-radius: 16px;
                width: 100%;
                max-width: 480px;
                margin: 16px;
                box-shadow: 0 20px 60px rgba(0,0,0,0.15);
                animation: seleccionIn 0.2s ease;
                overflow: hidden;
            }
            @keyframes seleccionIn {
                from { opacity: 0; transform: translateY(12px) scale(0.98); }
                to   { opacity: 1; transform: translateY(0) scale(1); }
            }

            .seleccion-modal-header {
                display: flex;
                align-items: flex-start;
                justify-content: space-between;
                padding: 24px 24px 16px;
                border-bottom: 1px solid var(--border);
            }
            .seleccion-modal-title {
                font-family: 'Playfair Display', serif;
                font-size: 18px;
                font-weight: 600;
                color: var(--navy);
                margin: 0 0 4px;
            }
            .seleccion-modal-sub {
                font-size: 12px;
                color: var(--muted);
                margin: 0;
            }
            .seleccion-close {
                background: none;
                border: none;
                color: var(--muted);
                cursor: pointer;
                padding: 4px;
                border-radius: 6px;
                transition: background 0.15s;
                flex-shrink: 0;
            }
            .seleccion-close:hover { background: var(--bg); }

            .seleccion-list {
                list-style: none;
                margin: 0;
                padding: 16px 24px 24px;
                display: flex;
                flex-direction: column;
                gap: 6px;
                max-height: 380px;
                overflow-y: auto;
            }
            .seleccion-list li {
                display: flex;
                align-items: center;
                gap: 10px;
                padding: 9px 12px;
                background: #FFFBF2;
                border: 1px solid #F5E0B0;
                border-radius: 8px;
                font-size: 13px;
                font-weight: 500;
                color: var(--navy);
                font-family: 'Courier New', monospace;
            }
            .seleccion-list li svg { flex-shrink: 0; color: #E8A020; }


            .sesion-empty {
                text-align: center;
                padding: 48px 20px;
                color: var(--muted);
                font-size: 14px;
            }

            .ayudante-subtitulo {
                font-family: 'Playfair Display', serif;
                font-size: 15px;
                font-weight: 600;
                color: var(--navy);
                margin: 24px 0 12px;
            }

            .btn-ayudante {
                display: inline-flex;
                align-items: center;
                gap: 7px;
                background: #fff;
                color: var(--navy, #1a2340);
                font-family: 'DM Sans', sans-serif;
                font-size: 13px;
                font-weight: 600;
                padding: 9px 18px;
                border-radius: 8px;
                border: 1.5px solid var(--navy, #1a2340);
                cursor: pointer;
                transition: background 0.15s, color 0.15s;
            }

            .btn-ayudante:hover {
                background: var(--navy, #1a2340);
                color: #fff;
            }

            .btn-ayudante svg {
                stroke: currentColor;
            }

            .ayudante-form {
                padding: 16px 24px 24px;
                display: flex;
                flex-direction: column;
                gap: 14px;
            }
            .ayudante-campo label {
                display: block;
                font-size: 12px;
                font-weight: 600;
                color: var(--navy);
                margin-bottom: 6px;
            }
            .ayudante-campo input,
            .ayudante-campo textarea {
                width: 100%;
                padding: 9px 12px;
                border: 1px solid var(--border);
                border-radius: 8px;
                font-size: 13px;
                font-family: inherit;
            }

            .postulante-item {
                display: flex;
                align-items: center;
                justify-content: space-between;
                padding: 10px 14px;
                border: 1px solid var(--border);
                border-radius: 8px;
                margin-bottom: 8px;
            }
            .postulante-nombre {
                font-size: 13px;
                font-weight: 600;
                color: var(--navy);
            }
            .postulante-acciones {
                display: flex;
                gap: 8px;
            }
            .btn-confirmar {
                background: #16a34a;
                color: #fff;
                border: none;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
            }
            .btn-rechazar {
                background: var(--white);
                color: #b91c1c;
                border: 1px solid #fecaca;
                padding: 6px 12px;
                border-radius: 6px;
                font-size: 12px;
                font-weight: 600;
                cursor: pointer;
            }

            .cupos-info {
                font-size: 12px;
                color: var(--muted);
                margin-bottom: 12px;
            }

            .btn-cancelar-solicitud {
                background: var(--white);
                color: var(--muted);
                border: 1px solid var(--border);
                padding: 4px 10px;
                border-radius: 6px;
                font-size: 11px;
                font-weight: 600;
                cursor: pointer;
                transition: background 0.15s, color 0.15s;
            }
            .btn-cancelar-solicitud:hover {
                background: #fef2f2;
                color: #b91c1c;
                border-color: #fecaca;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function verSeleccion(sesionId, cliente, fotos) {
                document.getElementById('seleccionSub').textContent =
                    cliente + ' · ' + fotos.length + ' foto' + (fotos.length !== 1 ? 's' : '') + ' seleccionada' + (fotos.length !== 1 ? 's' : '');

                const list = document.getElementById('seleccionList');
                list.innerHTML = fotos.map(f => `
                    <li>
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M15.232 5.232l3.536 3.536M9 13l6.586-6.586a2 2 0 112.828 2.828L11.828 15.828a4 4 0 01-1.414.828l-3 1 1-3a4 4 0 01.828-1.414z"/>
                        </svg>
                        ${f.nombre}
                    </li>
                `).join('');

                document.getElementById('seleccionModal').classList.add('open');
            }

            function cerrarSeleccion() {
                document.getElementById('seleccionModal').classList.remove('open');
            }

            document.getElementById('seleccionModal').addEventListener('click', function(e) {
                if (e.target === this) cerrarSeleccion();
            });

            function switchTab(name, el) {
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.getElementById('tab-' + name).classList.add('active');
                el.classList.add('active');
            }

            function abrirSolicitudAyudante(sesionId, tipo) {
                document.getElementById('ayudanteSub').textContent = tipo;
                document.getElementById('ayudanteForm').action = '/fotografo/sesiones/' + sesionId + '/solicitudes-ayudante';
                document.getElementById('ayudanteModal').classList.add('open');
            }

            function cerrarSolicitudAyudante() {
                document.getElementById('ayudanteModal').classList.remove('open');
            }

            document.getElementById('ayudanteModal').addEventListener('click', function(e) {
                if (e.target === this) cerrarSolicitudAyudante();
            });
        </script>
    @endpush
@endsection
