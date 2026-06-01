<div class="sesion-card">
    <div class="sesion-card-header">
        <div>
            <h3 class="sesion-tipo">
                {{ $sesion->reserva->paquete->nombre ?? 'Sesión' }}
                —
                {{ $sesion->reserva->cliente->usuario->persona->nombre ?? '' }}
                {{ $sesion->reserva->cliente->usuario->persona->apellido ?? '' }}
            </h3>
            <span class="sesion-id">#{{ $sesion->id }}</span>
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
        <div>
            @php $total = $sesion->fotografias->count(); @endphp
            @if($total > 0)
                <span class="fotos-badge">{{ $total }} foto{{ $total !== 1 ? 's' : '' }} subida{{ $total !== 1 ? 's' : '' }}</span>
            @endif
        </div>
        <div class="footer-actions">
            {{-- Botón provisional: pasar a EN_PROCESO --}}
            @if($sesion->estado === 'CONFIRMADA')
                <form method="POST" action="{{ route('fotografo.sesiones.iniciar', $sesion->id) }}">
                    @csrf
                    <button type="submit" class="btn-iniciar">Iniciar Sesión</button>
                </form>
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
