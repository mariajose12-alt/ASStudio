@extends('layouts.email')

@section('email_title', 'Comprobante pendiente de revisión')

@section('content')

    @php
        $pago = $reserva->pago;
        $comprobante = $pago->comprobante;
        $coincide = $comprobante?->montoCoincideCon((float) $pago->monto);
    @endphp

    {{-- Badge de estado --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.3);border-radius:20px;padding:6px 16px;">
                <span style="font-family:Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#e87722;">
                    Acción requerida
                </span>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#1a0d00;line-height:1.3;">
        Comprobante por revisar
    </h1>

    <p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        <strong style="color:#1a0d00;">{{ $pago->cliente->usuario->persona->nombre }} {{ $pago->cliente->usuario->persona->apellido }}</strong>
        subió un comprobante de transferencia para la sesión
        <strong style="color:#1a0d00;">{{ $pago->reserva->paquete->nombre }}</strong>.
    </p>

    {{-- Indicador de coincidencia automática --}}
    @if($coincide === true)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="background-color:#e9f7ef;border:1px solid rgba(39,174,96,0.25);border-radius:10px;margin-bottom:20px;">
            <tr>
                <td style="padding:16px 20px;">
                    <span style="font-family:Arial,sans-serif;font-size:13px;font-weight:600;color:#1e7e44;">
                        ✓ El monto detectado coincide con el monto esperado
                    </span>
                </td>
            </tr>
        </table>
    @elseif($coincide === false)
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="background-color:#fdecea;border:1px solid rgba(217,48,37,0.25);border-radius:10px;margin-bottom:20px;">
            <tr>
                <td style="padding:16px 20px;">
                    <span style="font-family:Arial,sans-serif;font-size:13px;font-weight:600;color:#c5341f;">
                        ⚠ El monto detectado NO coincide con el monto esperado — revisa con cuidado
                    </span>
                </td>
            </tr>
        </table>
    @else
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="background-color:#f4f4f4;border:1px solid #e0e0e0;border-radius:10px;margin-bottom:20px;">
            <tr>
                <td style="padding:16px 20px;">
                    <span style="font-family:Arial,sans-serif;font-size:13px;font-weight:600;color:#6b6b6b;">
                        ⓘ No se pudo leer el comprobante automáticamente — requiere revisión manual completa
                    </span>
                </td>
            </tr>
        </table>
    @endif

    {{-- Tabla comparativa: esperado vs detectado --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.2);border-radius:10px;margin-bottom:28px;">
        <tr>
            <td style="padding:24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Monto esperado</td>
                        <td align="right" style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:15px;font-weight:700;color:#1a0d00;">
                            RD$ {{ number_format($pago->monto, 2) }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Monto detectado</td>
                        <td align="right" style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:15px;font-weight:700;color:{{ $coincide === false ? '#c5341f' : '#1a0d00' }};">
                            {{ $comprobante?->monto_detectado ? 'RD$ ' . number_format($comprobante->monto_detectado, 2) : 'No detectado' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Fecha detectada</td>
                        <td align="right" style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1a1a;">
                            {{ $comprobante?->fecha_detectada?->format('d/m/Y') ?? 'No detectada' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Banco detectado</td>
                        <td align="right" style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1a1a;">
                            {{ $comprobante?->banco_detectado ?? 'No detectado' }}
                        </td>
                    </tr>
                    <tr>
                        <td style="font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Tipo de pago</td>
                        <td align="right" style="font-family:Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1a1a;">
                            {{ ucfirst(strtolower($pago->tipo)) }}
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 32px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        Abre el panel de pagos para ver la imagen del comprobante y aprobar o rechazar la transacción.
    </p>

    {{-- Botón CTA --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
        <tr>
            <td style="border-radius:999px;background-color:#e87722;">
                <a href="{{ route('admin.pagos.revisar', $pago->id) }}" target="_blank"
                   style="display:inline-block;padding:14px 36px;font-family:Arial,sans-serif;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:999px;letter-spacing:0.3px;">
                    Revisar comprobante
                </a>
            </td>
        </tr>
    </table>

@endsection
