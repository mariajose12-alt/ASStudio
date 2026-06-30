@extends('layouts.cliente')

@section('title', 'Pagos Pendientes')

@section('content')
    <div class="pp-page">

        @forelse($pagos as $pago)
            @php
                $etiquetas = [
                    'ANTICIPO'  => ['texto' => 'Anticipo',       'hint' => 'Reserva tu fecha con este pago inicial.'],
                    'FINAL'     => ['texto' => 'Pago final',     'hint' => 'Último pago para recibir tu galería.'],
                    'COMPLETO'  => ['texto' => 'Pago completo',  'hint' => 'Cubre el total de tu sesión.'],
                ];
                $etiqueta = $etiquetas[$pago->tipo] ?? ['texto' => ucfirst(strtolower($pago->tipo)), 'hint' => ''];
            @endphp

            <a href="{{ route('cliente.pagos.comprobante.form', $pago) }}"
               class="pp-card {{ $pago->estado === 'RECHAZADO' ? 'pp-card--rechazado' : '' }}">

                <div class="pp-card__top">
                    <div>
                        <div class="pp-card__paquete">{{ $pago->reserva->paquete->nombre ?? 'Sesión fotográfica' }}</div>
                        <div class="pp-card__tipo-badge">{{ $etiqueta['texto'] }}</div>
                        @if($pago->estado === 'RECHAZADO')
                            <div class="pp-card__rechazado-badge">Comprobante rechazado</div>
                        @endif
                    </div>
                    <div class="pp-card__monto">RD$ {{ number_format($pago->monto, 2) }}</div>
                </div>

                @if($etiqueta['hint'])
                    <p class="pp-card__hint">{{ $etiqueta['hint'] }}</p>
                @endif

                <div class="pp-card__footer" style="{{ $pago->estado === 'RECHAZADO' ? 'color:#c5341f;' : '' }}">
                    <span class="pp-card__cta">{{ $pago->estado === 'RECHAZADO' ? 'Subir nuevo comprobante' : 'Pagar ahora' }}</span>
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </div>

            </a>
        @empty
            <div class="pp-empty">
                <svg width="40" height="40" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p>Todo al día. No tienes pagos pendientes.</p>
            </div>
        @endforelse

    </div>

    <style>
        .pp-page {
            max-width: 480px;
            margin: 0 auto;
            padding: 28px 16px 80px;
            display: flex;
            flex-direction: column;
            gap: 14px;
        }

        .pp-card--rechazado {
            border-left-color: #c5341f;
            background: #fffaf9;
        }

        .pp-card__rechazado-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #c5341f;
            background: #fdecea;
            padding: 3px 10px;
            border-radius: 999px;
            margin-top: 4px;
        }

        .pp-card__motivo {
            font-size: 12px;
            color: #9a2a1c;
            background: #fdecea;
            border-radius: 8px;
            padding: 8px 12px;
            margin: 0;
            line-height: 1.5;
        }

        .pp-header { margin-bottom: 4px; }

        .pp-titulo {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 700;
            color: #1a0f00;
            margin: 0 0 4px;
        }

        .pp-sub {
            font-size: 14px;
            color: #9e8c7e;
            margin: 0;
        }

        /* Card */
        .pp-card {
            display: flex;
            flex-direction: column;
            gap: 10px;
            background: #fff;
            border: 1px solid #f0e6da;
            border-left: 4px solid #e87722;
            border-radius: 16px;
            padding: 20px 18px;
            text-decoration: none;
            color: #1a0f00;
            box-shadow: 0 2px 10px rgba(26,15,0,0.05);
            transition: transform 0.18s ease, box-shadow 0.18s ease;
        }

        .pp-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(232,119,34,0.15);
        }

        .pp-card__top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 12px;
        }

        .pp-card__paquete {
            font-size: 15px;
            font-weight: 700;
            color: #1a0f00;
            line-height: 1.2;
            margin-bottom: 6px;
        }

        .pp-card__tipo-badge {
            display: inline-block;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: #e87722;
            background: #fff3e8;
            padding: 3px 10px;
            border-radius: 999px;
        }

        .pp-card__monto {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 22px;
            font-weight: 700;
            color: #1a0f00;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .pp-card__hint {
            font-size: 13px;
            color: #9e8c7e;
            margin: 0;
            line-height: 1.4;
        }

        .pp-card__footer {
            display: flex;
            align-items: center;
            justify-content: flex-end;
            gap: 4px;
            color: #e87722;
            font-size: 13px;
            font-weight: 700;
            padding-top: 4px;
            border-top: 1px solid #f5ede4;
        }

        /* Empty */
        .pp-empty {
            text-align: center;
            padding: 48px 20px;
            color: #9e8c7e;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 12px;
        }

        .pp-empty svg { opacity: 0.4; }

        .pp-empty p {
            font-size: 14px;
            margin: 0;
        }
    </style>
@endsection
