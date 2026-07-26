@extends('layouts.email')

@section('email_title', 'Segundo pago disponible')

@section('content')

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.3);border-radius:20px;padding:6px 16px;">
                <span style="font-family:Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#e87722;">
                    Segundo pago
                </span>
            </td>
        </tr>
    </table>

    <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#1a0d00;line-height:1.3;">
        ¡Ya elegiste tus fotos favoritas!
    </h1>

    <p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        Nuestro equipo ya está trabajando en la edición de tu sesión
        <strong style="color:#1a0d00;">{{ $reserva->paquete->nombre }}</strong>.
        Para completar el proceso, falta el pago restante de tu sesión.
    </p>

    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.2);border-radius:10px;margin-bottom:28px;">
        <tr>
            <td style="padding:20px 24px;">
                <div style="font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;margin-bottom:4px;">Monto pendiente</div>
                <div style="font-family:Arial,sans-serif;font-size:20px;font-weight:700;color:#1a0d00;">
                    RD$ {{ number_format($pago->monto, 2) }}
                </div>
            </td>
        </tr>
    </table>

    <p style="margin:0 0 32px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        Una vez confirmemos tu comprobante, tus fotos editadas quedarán en camino.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto;">
        <tr>
            <td style="border-radius:999px;background-color:#e87722;">
                <a href="{{ route('cliente.pagos.comprobante.form', $pago->id) }}" target="_blank"
                   style="display:inline-block;padding:14px 36px;font-family:Arial,sans-serif;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:999px;letter-spacing:0.3px;">
                    Ir a pagar
                </a>
            </td>
        </tr>
    </table>

@endsection
