@extends('layouts.email')

@section('email_title', 'Necesitamos que revises tu comprobante')

@section('content')

    @php
        $pago = $reserva->pagos()->where('estado', 'RECHAZADO')->latest('fecha_completado')->first();
    @endphp

    {{-- Badge de estado --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="background-color:#fdecea;border:1px solid rgba(217,48,37,0.25);border-radius:20px;padding:6px 16px;">
                <span style="font-family:Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#c5341f;">
                    Comprobante rechazado
                </span>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#1a0d00;line-height:1.3;">
        Hola {{ $reserva->cliente->usuario->persona->nombre }}
    </h1>

    <p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        Revisamos el comprobante que enviaste para tu sesión
        <strong style="color:#1a0d00;">{{ $reserva->paquete->nombre }}</strong>
        y no pudimos confirmarlo. Este es el motivo:
    </p>

    {{-- Motivo del rechazo --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#fdecea;border:1px solid rgba(217,48,37,0.2);border-radius:10px;margin-bottom:28px;">
        <tr>
            <td style="padding:20px 24px;">
                <p style="margin:0;font-family:Arial,sans-serif;font-size:14px;color:#9a2a1c;line-height:1.6;">
                    {{ $pago->motivo_rechazo }}
                </p>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 32px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        No te preocupes, puedes subir un nuevo comprobante directamente desde tu panel.
        El monto pendiente sigue siendo
        <strong style="color:#1a0d00;">RD$ {{ number_format($pago->monto, 2) }}</strong>.
    </p>

    {{-- Botón CTA --}}
    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
        <tr>
            <td style="border-radius:999px;background-color:#e87722;">
                <a href="{{ route('cliente.pagos.comprobante.form', $pago->id) }}" target="_blank"
                   style="display:inline-block;padding:14px 36px;font-family:Arial,sans-serif;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:999px;letter-spacing:0.3px;">
                    Subir nuevo comprobante
                </a>
            </td>
        </tr>
    </table>

@endsection
