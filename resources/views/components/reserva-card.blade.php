@php
    $estado = strtolower($reserva->estado);

    $steps = [
        ['key' => 'pendiente',  'label' => 'Pendiente'],
        ['key' => 'aprobada',   'label' => 'Aprobada'],
        ['key' => 'en_sesion',  'label' => 'En sesión'],
        ['key' => 'finalizada', 'label' => 'Finalizada'],
    ];

    $stepOrder   = ['pendiente' => 0, 'aprobada' => 1, 'en_sesion' => 2, 'finalizada' => 3, 'cancelada' => 3];
    $currentStep = $stepOrder[$estado] ?? 0;

    $progressMap = ['pendiente' => 8, 'aprobada' => 38, 'en_sesion' => 68, 'finalizada' => 100, 'cancelada' => 100];
    $progress    = $progressMap[$estado] ?? 8;

    $tipo = strtolower($reserva->catalogo->nombre ?? '');
    $iconClass = match(true) {
        str_contains($tipo, 'estudio') || str_contains($tipo, 'studio')   => 'icon-estudio',
        str_contains($tipo, 'exterior') || str_contains($tipo, 'outdoor') => 'icon-exterior',
        str_contains($tipo, 'boudoir')                                     => 'icon-boudoir',
        default                                                             => 'icon-default',
    };

    $esSugerencia = $estado === 'modificacion_propuesta';
@endphp

{{-- Si es sugerencia pendiente: div clickeable que abre modal --}}
{{-- Si no: enlace normal al detalle --}}
@if($esSugerencia)
    <div class="reserva-card reserva-card--alerta"
         onclick="abrirModalSugerencia({{ $reserva->id }}, @js($reserva->motivo_rechazo ?? ''), {{ $reserva->fotografo_id ?? 'null' }})"
         style="cursor:pointer;">
        @else
            <a href="{{ route('cliente.reservas.show', $reserva->id) }}" class="reserva-card">
                @endif

                {{-- Ícono --}}
                <div class="reserva-icon {{ $iconClass }}">
                    @if($iconClass === 'icon-estudio')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                        </svg>
                    @elseif($iconClass === 'icon-exterior')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 9l9-7 9 7v11a1 1 0 01-1 1H4a1 1 0 01-1-1z"/>
                            <polyline stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                      points="9 22 9 12 15 12 15 22"/>
                        </svg>
                    @elseif($iconClass === 'icon-boudoir')
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                    @else
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                  d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    @endif
                </div>

                {{-- Cuerpo --}}
                <div class="reserva-body">

                    <div class="reserva-title">{{ $reserva->catalogo->nombre ?? 'Sesión fotográfica' }}</div>
                    <div class="reserva-date">
                        {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->isoFormat('dddd D [de] MMMM, YYYY') }}
                        · {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('g:i A') }}
                    </div>

                    {{-- Stepper --}}
                    <div class="reserva-stepper">

                        {{-- Barra de progreso --}}
                        <div class="stepper-track">
                            @if($esSugerencia)
                                {{-- Barra ámbar para sugerencia pendiente --}}
                                <div class="stepper-fill" style="width:8%; background:#ffc107;"></div>
                            @else
                                <div class="stepper-fill fill-{{ $estado }}" style="width:{{ $progress }}%"></div>
                            @endif
                        </div>

                        {{-- Pills --}}
                        <div class="stepper-pills">
                            @if($estado === 'cancelada')
                                <span class="step-pill cancelada">✕ Cancelada</span>
                            @elseif($esSugerencia)
                                <span class="step-pill" style="background:#fff3cd; color:#856404; border:1px solid #ffc107;">
                        ✎ Sugerencia pendiente — toca para responder
                    </span>
                            @else
                                @foreach($steps as $i => $step)
                                    @php
                                        $isDone    = $i < $currentStep;
                                        $isCurrent = $i === $currentStep;
                                        $pillClass = $isDone ? 'done' : ($isCurrent ? "current-{$estado}" : '');
                                    @endphp
                                    <span class="step-pill {{ $pillClass }}">
                            @if($isDone) ✓ @endif
                                        {{ $step['label'] }}
                        </span>
                                @endforeach
                            @endif
                        </div>
                    </div>

                </div>

                {{-- Meta derecha --}}
                <div class="reserva-meta">
                    @if($esSugerencia)
                        <span class="estado-badge" style="background:#fff3cd; color:#856404; border:1px solid #ffc107; white-space:nowrap;">
                ✎ Revisión
            </span>
                    @else
                        <span class="estado-badge badge-{{ $estado }}">
                {{ ucfirst(str_replace('_', ' ', $estado)) }}
            </span>
                    @endif

                    @if($reserva->precio_total ?? false)
                        <div class="reserva-precio">RD${{ number_format($reserva->precio_total, 0, '.', ',') }}</div>
                    @endif
                    @if($reserva->paquete->nombre ?? false)
                        <div class="reserva-paquete">{{ $reserva->paquete->nombre }}</div>
                    @endif
                </div>

            @if($esSugerencia)
    </div>
    @else
        </a>
@endif
