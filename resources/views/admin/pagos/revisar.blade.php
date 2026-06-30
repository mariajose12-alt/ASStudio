@extends('layouts.admin')

@section('title', 'Revisar pago')

@section('content')
    <div class="revisar-pago-page">

        <div class="revisar-pago-grid">

            {{-- Columna izquierda: imagen del comprobante --}}
            <div class="revisar-pago-imagen-col">
                <h2 class="revisar-pago-subtitulo">Comprobante</h2>
                @if($pago->comprobante)
                    <img src="{{ $pago->comprobante->urlFirmada() }}"
                         alt="Comprobante de pago"
                         class="revisar-pago-imagen">
                @else
                    <div class="revisar-pago-sin-comprobante">
                        No hay comprobante asociado a este pago todavía.
                    </div>
                @endif
            </div>

            {{-- Columna derecha: datos y acciones --}}
            <div class="revisar-pago-detalle-col">

                <h1 class="revisar-pago-titulo">Revisar pago</h1>
                <p class="revisar-pago-cliente">
                    {{ $pago->reserva->cliente->usuario->persona->nombre }}
                    {{ $pago->reserva->cliente->usuario->persona->apellido }}
                    &middot;
                    {{ $pago->reserva->paquete->nombre }}
                </p>

                @if(session('success'))
                    <div class="revisar-pago-alerta revisar-pago-alerta--ok">{{ session('success') }}</div>
                @endif
                @if(session('error'))
                    <div class="revisar-pago-alerta revisar-pago-alerta--error">{{ session('error') }}</div>
                @endif

                {{-- Indicador de coincidencia OCR --}}
                @php
                    $coincide = $pago->comprobante?->montoCoincideCon((float) $pago->monto);
                @endphp

                @if($coincide === true)
                    <div class="revisar-pago-badge revisar-pago-badge--ok">
                        ✓ El monto detectado coincide
                    </div>
                @elseif($coincide === false)
                    <div class="revisar-pago-badge revisar-pago-badge--error">
                        ⚠ El monto detectado NO coincide
                    </div>
                @else
                    <div class="revisar-pago-badge revisar-pago-badge--neutral">
                        ⓘ No se pudo leer el comprobante automáticamente
                    </div>
                @endif

                {{-- Tabla comparativa --}}
                <table class="revisar-pago-tabla">
                    <tr>
                        <td>Monto esperado</td>
                        <td class="revisar-pago-tabla__valor">RD$ {{ number_format($pago->monto, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Monto detectado</td>
                        <td class="revisar-pago-tabla__valor" style="{{ $coincide === false ? 'color:#c5341f' : '' }}">
                            {{ $pago->comprobante?->monto_detectado ? 'RD$ ' . number_format($pago->comprobante->monto_detectado, 2) : 'No detectado' }}
                        </td>
                    </tr>
                    <tr>
                        <td>Fecha detectada</td>
                        <td class="revisar-pago-tabla__valor">{{ $pago->comprobante?->fecha_detectada?->format('d/m/Y') ?? 'No detectada' }}</td>
                    </tr>
                    <tr>
                        <td>Banco detectado</td>
                        <td class="revisar-pago-tabla__valor">{{ $pago->comprobante?->banco_detectado ?? 'No detectado' }}</td>
                    </tr>
                    <tr>
                        <td>Referencia detectada</td>
                        <td class="revisar-pago-tabla__valor">{{ $pago->comprobante?->referencia_detectada ?? 'No detectada' }}</td>
                    </tr>
                    <tr>
                        <td>Tipo de pago</td>
                        <td class="revisar-pago-tabla__valor">{{ ucfirst(strtolower($pago->tipo)) }}</td>
                    </tr>
                </table>

                {{-- Acciones --}}
                <div class="revisar-pago-acciones">
                    <form method="POST" action="{{ route('admin.pagos.aprobar', $pago->id) }}">
                        @csrf
                        <button type="submit" class="revisar-pago-btn revisar-pago-btn--aprobar">
                            Aprobar pago
                        </button>
                    </form>

                    <button type="button" class="revisar-pago-btn revisar-pago-btn--rechazar" onclick="document.getElementById('formRechazo').classList.toggle('abierto')">
                        Rechazar pago
                    </button>
                </div>

                {{-- Formulario de rechazo (oculto por defecto) --}}
                <div class="revisar-pago-rechazo-form" id="formRechazo">
                    <form method="POST" action="{{ route('admin.pagos.rechazar', $pago->id) }}">
                        @csrf
                        <label for="motivo" class="revisar-pago-label">Motivo del rechazo</label>
                        <textarea name="motivo" id="motivo" rows="3" required
                                  placeholder="Ej: el monto transferido no coincide con el esperado"
                                  class="revisar-pago-textarea"></textarea>
                        <button type="submit" class="revisar-pago-btn revisar-pago-btn--rechazar-confirmar">
                            Confirmar rechazo
                        </button>
                    </form>
                </div>

            </div>

        </div>

    </div>

    <style>
        .revisar-pago-page { padding: 32px; max-width: 1100px; margin: 0 auto; }
        .revisar-pago-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 32px;
        }
        @media (max-width: 720px) {
            .revisar-pago-grid { grid-template-columns: 1fr; }
        }

        .revisar-pago-subtitulo {
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9ca3af;
            margin: 0 0 12px;
        }
        .revisar-pago-imagen {
            width: 100%;
            border-radius: 12px;
            border: 1px solid #e8e8e8;
        }
        .revisar-pago-sin-comprobante {
            background: #f4f4f4;
            border-radius: 12px;
            padding: 40px 20px;
            text-align: center;
            color: #9ca3af;
            font-family: Arial, sans-serif;
            font-size: 14px;
        }

        .revisar-pago-titulo {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 26px;
            font-weight: 600;
            color: #1a0f00;
            margin: 0 0 6px;
        }
        .revisar-pago-cliente {
            font-family: Arial, sans-serif;
            font-size: 14px;
            color: #6b6b6b;
            margin: 0 0 20px;
        }

        .revisar-pago-alerta {
            border-radius: 10px;
            padding: 12px 16px;
            margin-bottom: 16px;
            font-family: Arial, sans-serif;
            font-size: 13px;
        }
        .revisar-pago-alerta--ok    { background: #e9f7ef; color: #1e7e44; }
        .revisar-pago-alerta--error { background: #fdecea; color: #9a2a1c; }

        .revisar-pago-badge {
            display: inline-block;
            border-radius: 999px;
            padding: 8px 16px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 20px;
        }
        .revisar-pago-badge--ok      { background: #e9f7ef; color: #1e7e44; }
        .revisar-pago-badge--error   { background: #fdecea; color: #c5341f; }
        .revisar-pago-badge--neutral { background: #f4f4f4; color: #6b6b6b; }

        .revisar-pago-tabla {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 28px;
            background: #fff3e8;
            border: 1px solid rgba(232,119,34,0.2);
            border-radius: 10px;
            overflow: hidden;
        }
        .revisar-pago-tabla td {
            padding: 12px 18px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            color: #9ca3af;
            border-bottom: 1px solid rgba(232,119,34,0.12);
        }
        .revisar-pago-tabla tr:last-child td { border-bottom: none; }
        .revisar-pago-tabla__valor {
            text-align: right;
            font-weight: 600;
            color: #1a0f00 !important;
        }

        .revisar-pago-acciones {
            display: flex;
            gap: 12px;
            margin-bottom: 16px;
        }
        .revisar-pago-acciones form { flex: 1; }
        .revisar-pago-btn {
            width: 100%;
            border: none;
            border-radius: 999px;
            padding: 14px;
            font-family: Arial, sans-serif;
            font-size: 14px;
            font-weight: 700;
            cursor: pointer;
        }
        .revisar-pago-btn--aprobar {
            background-color: #e87722;
            color: #ffffff;
        }
        .revisar-pago-btn--rechazar {
            background-color: transparent;
            border: 1px solid #c5341f;
            color: #c5341f;
        }

        .revisar-pago-rechazo-form {
            display: none;
            background: #fdecea;
            border-radius: 12px;
            padding: 20px;
        }
        .revisar-pago-rechazo-form.abierto { display: block; }
        .revisar-pago-label {
            display: block;
            font-family: Arial, sans-serif;
            font-size: 13px;
            font-weight: 600;
            color: #9a2a1c;
            margin-bottom: 8px;
        }
        .revisar-pago-textarea {
            width: 100%;
            border: 1px solid rgba(217,48,37,0.3);
            border-radius: 8px;
            padding: 10px 12px;
            font-family: Arial, sans-serif;
            font-size: 13px;
            margin-bottom: 12px;
            resize: vertical;
            box-sizing: border-box;
        }
        .revisar-pago-btn--rechazar-confirmar {
            background-color: #c5341f;
            color: #ffffff;
        }
    </style>
@endsection
