@extends('layouts.cliente')
@section('title', 'Galería')

@section('content')
    <div class="galeria-grid">
        @forelse($sesiones as $sesion)
            @php
                $totalFotos = $sesion->fotografias->count();
                $fotoPortada = $sesion->fotografias->first();
                $badge = match($sesion->estado) {
                    'GALERIA_DISPONIBLE' => ['texto' => 'Selecciona tus fotos',  'clase' => 'badge--pendiente'],
                    'EN_EDICION'         => ['texto' => 'En edición',            'clase' => 'badge--edicion'],
                    'FINALIZADA'         => ['texto' => 'Listas para descargar', 'clase' => 'badge--lista'],
                    default              => null,
                };
            @endphp
            <div class="galeria-card">
                <div class="galeria-thumb">
                    @if($fotoPortada)
                        <img src="{{ $fotoPortada->url_thumb_firmada ?? $fotoPortada->url_firmada }}"
                             alt="Thumbnail sesión"
                             style="width:100%; height:100%; object-fit:cover;">
                    @else
                        <div style="width:100%; height:100%; background:var(--navy,#f0f0f0); display:flex; align-items:center; justify-content:center; color:var(--muted); font-size:12px;">
                            Sin fotos aún
                        </div>
                    @endif
                    @if($badge)
                        <span class="galeria-badge {{ $badge['clase'] }}">{{ $badge['texto'] }}</span>
                    @endif
                </div>
                <div class="galeria-card-body">
                    <h3 class="galeria-nombre">
                        {{ $sesion->reserva->cliente->usuario->persona->nombre }}
                        — {{ $sesion->reserva->paquete->nombre }}
                    </h3>
                    <p class="galeria-fecha">{{ $sesion->fecha_inicio->format('d \d\e F, Y') }}</p>
                    <div class="galeria-fotografo">
                        <div class="fotografo-avatar">
                            {{ strtoupper(substr($sesion->reserva->fotografo->empleado->usuario->persona->nombre ?? 'F', 0, 1)) }}
                        </div>
                        <span>{{ $sesion->reserva->fotografo->empleado->usuario->persona->nombre ?? '—' }}</span>
                    </div>
                    <a href="{{ route('cliente.galeria.show', $sesion->id) }}" class="btn-ver-galeria">
                        {{ match($sesion->estado) {
                            'GALERIA_DISPONIBLE' => 'Seleccionar fotos',
                            'EN_EDICION'         => 'Ver galería',
                            'FINALIZADA'         => 'Ver y descargar',
                            default              => 'Ver galería',
                        } }}
                    </a>
                </div>
            </div>
        @empty
            <p style="grid-column:1/-1; text-align:center; padding:60px 0; color:var(--muted); font-size:15px;">
                No tienes galerías disponibles aún.
            </p>
        @endforelse
    </div>
@endsection
