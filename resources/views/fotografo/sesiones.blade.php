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

            .empty-state {
                text-align: center;
                padding: 48px 20px;
                color: var(--muted);
                font-size: 14px;
            }
        </style>
    @endpush

    @push('scripts')
        <script>
            function switchTab(name, el) {
                document.querySelectorAll('.tab-content').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.getElementById('tab-' + name).classList.add('active');
                el.classList.add('active');
            }
        </script>
    @endpush
@endsection
