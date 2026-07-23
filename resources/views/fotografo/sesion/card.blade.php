<div class="sesion-card">
    <div class="sesion-card-header">
        <div>
            <h3 class="sesion-tipo">
                {{ $sesion->reserva->paquete->nombre ?? 'Sesión' }}
                —
                {{ $sesion->reserva->cliente->usuario->persona->nombre ?? '' }}
                {{ $sesion->reserva->cliente->usuario->persona->apellido ?? '' }}
            </h3>
        </div>
        <span class="badge-estado {{ strtolower($sesion->estado) }}">
            {{ str_replace('_', ' ', $sesion->estado) }}
        </span>
    </div>

    <div class="sesion-meta">
        <div class="meta-item">
            <span class="meta-label">FECHA</span>
            <span class="meta-value">{{ $sesion->fecha_inicio->format('d \d\e F, Y') }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">HORARIO</span>
            <span class="meta-value">{{ $sesion->fecha_inicio->format('H:i') }} — {{ $sesion->fecha_fin->format('H:i') }} hrs</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">UBICACIÓN</span>
            <span class="meta-value">{{ $reserva->lugar ?? 'Estudio' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">TIPO</span>
            <span class="meta-value">{{ $sesion->reserva->tipo ?? '—' }}</span>
        </div>
    </div>

    <div class="sesion-card-footer">
        <div class="footer-actions">
            {{-- Botón provisional: pasar a EN_PROCESO --}}
            @if($sesion->estado === 'CONFIRMADA' && $sesion->reserva->fotografo_id === $fotografo->id)
                <form method="POST" action="{{ route('fotografo.sesiones.iniciar', $sesion->id) }}">
                    @csrf
                    <button type="submit" class="btn-iniciar">Iniciar Sesión</button>
                </form>
            @endif

            {{-- Solicitar ayudantes --}}
            @php $yaSolicito = $sesion->solicitudesAyudante->isNotEmpty(); @endphp

            @if(
                $sesion->estado === 'CONFIRMADA' &&
                $sesion->reserva->fotografo_id === $fotografo->id &&
                ! $yaSolicito)

                <button type="button"
                        class="btn-ayudante"
                        onclick="abrirSolicitudAyudante({{ $sesion->id }}, {{ Illuminate\Support\Js::from($sesion->reserva->tipo) }})">

                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                    </svg>
                    Solicitar ayudantes
                </button>
            @endif

            {{-- Ver selección del cliente (solo EN_EDICION y si hay fotos seleccionadas) --}}
            @if($sesion->estado === 'EN_EDICION')
                @php $seleccionadas = $sesion->fotografias->where('seleccionada', true)->where('estado', 'PENDIENTE_EDICION'); @endphp
                @if($seleccionadas->count() > 0)
                    <button type="button" class="btn-seleccion"
                            onclick="verSeleccion(
                                {{ $sesion->id }},
                                {{ Illuminate\Support\Js::from($sesion->reserva->cliente->usuario->persona->nombre . ' ' . $sesion->reserva->cliente->usuario->persona->apellido) }},
                                {{ $seleccionadas->map(fn($f) => ['nombre' => $f->nombre_original ?? basename($f->url)])->values()->toJson() }}
                            )">
                        <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                        Ver selección
                        <span class="btn-seleccion-badge">{{ $seleccionadas->count() }}</span>
                    </button>
                @endif
            @endif

            {{-- Subir fotos solo si está EN_PROCESO o EN_EDICION --}}
            @if(in_array($sesion->estado, ['EN_PROCESO', 'EN_EDICION']))
                <a href="{{ route('fotografo.fotografias.create', $sesion->id) }}" class="btn-subir">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/>
                    </svg>
                    Subir Fotografías
                </a>
            @endif
        </div>
    </div>
</div>
