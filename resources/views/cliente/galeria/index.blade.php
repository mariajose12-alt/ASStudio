@extends('layouts.cliente')
@section('title', 'Galería')

@section('content')
    <div class="galeria-grid">
        @forelse($sesiones as $sesion)
            @php
                $totalFotos = $sesion->fotografias->count();
            @endphp
            <div class="galeria-card">
                <div class="galeria-thumb">
                    <img src="https://picsum.photos/seed/{{ $sesion->id }}/400/200"
                         alt="Thumbnail sesión"
                         style="width:100%; height:100%; object-fit:cover;">
                    @if($totalFotos > 0)
                        <span class="galeria-badge">{{ $totalFotos }} fotos</span>
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
                        Ver Galería
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
