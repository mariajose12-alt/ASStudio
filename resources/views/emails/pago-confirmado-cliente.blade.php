@extends('layouts.email')

@section('email_title', 'Tu pago ha sido confirmado')

@section('content')

    {{-- Badge de estado --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.3);border-radius:20px;padding:6px 16px;">
                <span style="font-family:Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#e87722;">
                    Pago confirmado
                </span>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#1a0d00;line-height:1.3;">
        ¡Hola {{ $reserva->cliente->usuario->persona->nombre }}!
    </h1>

    <p style="margin:0 0 28px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        Hemos confirmado tu pago correspondiente a la sesión
        <strong style="color:#1a0d00;">{{ $reserva->paquete->nombre }}</strong>.
        A continuación los detalles:
    </p>

    @if($pago)
        {{-- Tabla de detalles del pago --}}
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
               style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.2);border-radius:10px;margin-bottom:28px;">
            <tr>
                <td style="padding:24px;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Monto</td>
                            <td align="right" style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:15px;font-weight:700;color:#1a0d00;">
                                RD$ {{ number_format($pago->monto, 2) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Tipo de pago</td>
                            <td align="right" style="padding-bottom:14px;font-family:Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1a1a;">
                                {{ ucfirst(strtolower($pago->tipo)) }}
                            </td>
                        </tr>
                        <tr>
                            <td style="font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;">Fecha de confirmación</td>
                            <td align="right" style="font-family:Arial,sans-serif;font-size:14px;font-weight:500;color:#1a1a1a;">
                                {{ $pago->fecha_completado->translatedFormat('d \\d\\e F, Y') }}
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>
    @else
        <p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
            No pudimos encontrar el detalle de este pago. Por favor contacta a soporte.
        </p>
    @endif
@endsection
