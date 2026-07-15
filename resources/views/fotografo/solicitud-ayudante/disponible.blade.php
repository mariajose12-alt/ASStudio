<div class="sesion-card">
    <div class="sesion-card-header">
        <div>
            <h3 class="sesion-tipo">
                {{ $solicitud->sesion->reserva->tipo }}
            </h3>
            <span class="sesion-id">
                Fotógrafo principal: {{ $solicitud->solicitante->getPersona()->nombre }} {{ $solicitud->solicitante->getPersona()->apellido }}
            </span>
        </div>
        <span class="badge-estado en_edicion">
            {{ $solicitud->cuposDisponibles() }} cupo(s)
        </span>
    </div>

    <div class="sesion-meta">
        <div class="meta-item">
            <span class="meta-label">FECHA</span>
            <span class="meta-value">{{ $solicitud->sesion->fecha_inicio->format('d \d\e F, Y') }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">HORARIO</span>
            <span class="meta-value">{{ $solicitud->sesion->fecha_inicio->format('H:i') }} hrs</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">UBICACIÓN</span>
            <span class="meta-value">{{ $solicitud->sesion->lugar ?? 'Estudio' }}</span>
        </div>
        <div class="meta-item">
            <span class="meta-label">TIPO</span>
            <span class="meta-value">{{ $solicitud->sesion->reserva->tipo }}</span>
        </div>
    </div>

    @if($solicitud->mensaje)
        <p class="cupos-info">"{{ $solicitud->mensaje }}"</p>
    @endif

    <div class="sesion-card-footer">
        <form method="POST" action="{{ route('fotografo.solicitudes-ayudante.postularse', $solicitud->id) }}">
            @csrf
            <button type="submit" class="btn-iniciar">Postularme</button>
        </form>
    </div>
</div>
