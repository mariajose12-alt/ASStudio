@extends('layouts.cliente')
@section('title', '')

@section('content')
    <div class="galeria-topbar">
        <a href="{{ route('cliente.galeria') }}" class="btn-volver">← Volver</a>
        <h2 class="galeria-titulo">
            {{ $sesion->reserva->cliente->usuario->persona->nombre }}
            — {{ $sesion->reserva->paquete->nombre }}
        </h2>
        <div style="width: 80px;"></div>
    </div>

    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom: 20px;">{{ session('success') }}</div>
    @endif

    @if($fotos->count() > 0)
        <div class="fotos-grid">
            @foreach($fotos as $foto)
                <div class="foto-item">
                    <img src="{{ $foto->url_firmada }}" alt="Foto {{ $foto->id }}" loading="lazy">
                    <a href="{{ route('cliente.galeria.descargar', $foto->id) }}" class="foto-download" title="Descargar">
                        <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                    </a>
                </div>
            @endforeach
        </div>

        {{-- Confirmar recepción solo si aún no está finalizada --}}
        @if($sesion->estado !== 'FINALIZADA')
            <div class="confirmar-recepcion">
                <p class="confirmar-texto">¿Todo luce bien con tus fotografías?</p>
                <form method="POST" action="{{ route('cliente.galeria.recepcion', $sesion->id) }}">
                    @csrf
                    <button type="submit" class="btn-confirmar-recepcion">
                        Confirmar recepción
                    </button>
                </form>
            </div>
        @else
            <div class="finalizada-banner">
                <svg width="18" height="18" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                </svg>
                Galería finalizada — tus fotos están listas para descargar
            </div>
        @endif

    @else
        <div style="text-align:center; padding: 60px 0; color: var(--muted);">
            Las fotos editadas estarán disponibles pronto.
        </div>
    @endif

    @push('styles')
        <style>
            .galeria-topbar {
                display: flex;
                align-items: center;
                justify-content: space-between;
                margin-bottom: 24px;
            }

            .btn-volver {
                font-size: 13px;
                font-weight: 500;
                color: var(--navy);
                background: var(--white);
                border: 1px solid var(--border);
                padding: 7px 14px;
                border-radius: 8px;
                text-decoration: none;
            }

            .galeria-titulo {
                font-family: 'Playfair Display', serif;
                font-size: 17px;
                font-weight: 600;
                color: var(--navy);
                margin: 0;
                text-align: center;
            }

            .fotos-grid {
                display: grid;
                grid-template-columns: repeat(4, 1fr);
                gap: 10px;
                margin-bottom: 32px;
            }
            @media (max-width: 768px) { .fotos-grid { grid-template-columns: repeat(2, 1fr); } }

            .foto-item {
                position: relative;
                aspect-ratio: 1;
                border-radius: 8px;
                overflow: hidden;
                background: #e8e8e8;
            }

            .foto-item img {
                width: 100%;
                height: 100%;
                object-fit: cover;
                display: block;
                transition: transform 0.2s;
            }
            .foto-item:hover img { transform: scale(1.03); }

            .foto-download {
                position: absolute;
                bottom: 8px;
                right: 8px;
                width: 32px;
                height: 32px;
                background: rgba(0,0,0,0.55);
                border-radius: 6px;
                display: flex;
                align-items: center;
                justify-content: center;
                color: #fff;
                opacity: 0;
                transition: opacity 0.15s;
                text-decoration: none;
            }
            .foto-item:hover .foto-download { opacity: 1; }

            .confirmar-recepcion {
                display: flex;
                align-items: center;
                justify-content: space-between;
                background: var(--white);
                border: 1px solid var(--border);
                border-radius: 12px;
                padding: 20px 24px;
            }

            .confirmar-texto {
                font-size: 14px;
                color: var(--navy);
                margin: 0;
            }

            .btn-confirmar-recepcion {
                background: var(--navy, #1a2340);
                color: #fff;
                border: none;
                padding: 11px 24px;
                border-radius: 8px;
                font-size: 14px;
                font-weight: 600;
                cursor: pointer;
                transition: opacity 0.15s;
            }
            .btn-confirmar-recepcion:hover { opacity: 0.85; }

            .finalizada-banner {
                display: flex;
                align-items: center;
                gap: 10px;
                background: #d1fae5;
                color: #065f46;
                font-size: 14px;
                font-weight: 500;
                padding: 14px 20px;
                border-radius: 10px;
            }
        </style>
    @endpush
@endsection
