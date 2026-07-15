<div class="sesion-card">
    <div class="sesion-card-header">
        <div>
            <h3 class="sesion-tipo">
                {{ $solicitud->sesion->reserva->tipo }}
                —
                {{ $solicitud->sesion->reserva->cliente->usuario->persona->nombre ?? '' }}
            </h3>
        </div>
        <div style="display:flex; align-items:center; gap:8px;">
            <span class="badge-estado en_proceso">Tu solicitud</span>
            <form method="POST" action="{{ route('fotografo.solicitudes-ayudante.cancelar', $solicitud->id) }}"
                  onsubmit="return confirm('¿Cancelar esta solicitud? Los ayudantes ya confirmados se mantienen, pero dejarás de aceptar más postulantes.');">
                @csrf
                <button type="submit" class="btn-cancelar-solicitud">Cancelar</button>
            </form>
        </div>
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
            <span class="meta-label">CUPOS</span>
            <span class="meta-value">{{ $solicitud->cupos_confirmados }} / {{ $solicitud->cantidad_ayudantes }}</span>
        </div>
    </div>

    @if($solicitud->postulacionesPendientes()->exists())
        <p class="cupos-info">Postulantes pendientes de confirmar:</p>
        @foreach($solicitud->postulaciones->where('estado', 'PENDIENTE') as $postulacion)
            <div class="postulante-item">
                <span class="postulante-nombre">
                    {{ $postulacion->fotografo->getPersona()->nombre }}
                    {{ $postulacion->fotografo->getPersona()->apellido }}
                </span>
                <div class="postulante-acciones">
                    <form method="POST" action="{{ route('fotografo.solicitudes-ayudante.confirmar', [$solicitud->id, $postulacion->id]) }}">
                        @csrf
                        <button type="submit" class="btn-confirmar">Confirmar</button>
                    </form>
                    <form method="POST" action="{{ route('fotografo.solicitudes-ayudante.rechazar', [$solicitud->id, $postulacion->id]) }}">
                        @csrf
                        <button type="submit" class="btn-rechazar">Rechazar</button>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <p class="cupos-info">Aún no hay postulantes.</p>
    @endif
</div>
