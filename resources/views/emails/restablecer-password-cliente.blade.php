@extends('layouts.email')

@section('email_title', 'Restablece tu contraseña')

@section('content')

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 0 24px;">
        <tr>
            <td style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.3);border-radius:20px;padding:6px 16px;">
                <span style="font-family:Arial,sans-serif;font-size:12px;font-weight:700;letter-spacing:0.5px;text-transform:uppercase;color:#e87722;">
                    Solicitud de restablecimiento
                </span>
            </td>
        </tr>
    </table>

    <h1 style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:600;color:#1a0d00;line-height:1.3;">
        Restablece tu contraseña
    </h1>

    <p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:15px;color:#4a4a4a;line-height:1.7;">
        Recibimos una solicitud para restablecer la contraseña de tu cuenta
        <strong style="color:#1a0d00;">{{ $usuario->email }}</strong>.
        Haz clic en el botón de abajo para configurar una nueva contraseña.
    </p>

    <table role="presentation" cellpadding="0" cellspacing="0" border="0" style="margin:0 auto 28px;">
        <tr>
            <td style="border-radius:999px;background-color:#e87722;">
                <a href="{{ $urlActivacion }}" target="_blank"
                   style="display:inline-block;padding:14px 36px;font-family:Arial,sans-serif;font-size:14px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:999px;letter-spacing:0.3px;">
                    Restablecer mi contraseña
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:0;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;line-height:1.6;">
        Este enlace expira en 48 horas. Si no solicitaste este cambio, puedes ignorar este correo — tu contraseña actual seguirá funcionando.
    </p>

@endsection
