@extends('layouts.fotografo')
@section('title', 'Sesiones')
@section('subtitle', 'Gestiona tus sesiones activas')

@section('content')

    {{-- Pestañas --}}
    <div class="tabs">
        <button class="tab active" onclick="switchTab('en-proceso', this)">
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
    </div>

    {{-- En Proceso --}}
    <div id="tab-en-proceso" class="tab-content active">
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

    @push('styles')
        <style>
            .tabs {
                display: flex;
                gap: 4px;
                border-bottom: 2px solid var(--border);
                margin-bottom: 24px;
            }

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

            .tab.active {
                color: var(--navy);
                border-bottom-color: #E8A020;
                font-weight: 600;
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
                padding: 9px 18px;
                border-radius: 8px;
                border: none;
                cursor: pointer;
                transition: opacity 0.15s;
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


            text-align: center;
            padding: 48px 20px;
            color: var(--muted);
            font-size: 14px;
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

            // Cerrar al hacer clic en el backdrop
            document.getElementById('seleccionModal').addEventListener('click', function(e) {
                if (e.target === this) cerrarSeleccion();
            });

            function switchTab(name, el) {
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.getElementById('tab-' + name).classList.add('active');
                el.classList.add('active');
            }
        </script>
    @endpush
@endsection
