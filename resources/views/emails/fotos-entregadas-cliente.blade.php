@extends('layouts.email')

@section('email_title', 'Fotos Entregadas')

@section('content')

    {{-- Ícono de estado --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:24px;">
                <div style="display:inline-block;width:56px;height:56px;border-radius:50%;background-color:#fff3e8;border:2px solid rgba(232,119,34,0.3);text-align:center;line-height:56px;font-size:24px;">
                    🎉
                </div>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 12px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:700;color:#1a0d00;line-height:1.3;text-align:center;">
        Tus fotos editadas están listas
    </h1>

    {{-- Subtítulo --}}
    <p style="margin:0 0 32px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#4b5563;text-align:center;">
        Hola <strong style="color:#1a0d00;">{{ $sesion->reserva->cliente->usuario->persona->nombre }} {{ $sesion->reserva->cliente->usuario->persona->apellido }}</strong>,
        tu fotógrafo finalizó la edición y ya puedes descargar tus fotografías.
    </p>

    {{-- Divisor --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
        <tr>
            <td style="height:1px;background-color:#e8e8e8;font-size:1px;line-height:1px;">&nbsp;</td>
        </tr>
    </table>

    {{-- Etiqueta sección --}}
    <p style="margin:0 0 14px;font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;">
        Detalles de la sesión
    </p>

    {{-- Tabla de detalles --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="border:1px solid #e8e8e8;border-radius:10px;overflow:hidden;">
        <tr>
            <td style="padding:14px 20px;background-color:#fafafa;border-bottom:1px solid #e8e8e8;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Paquete</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:600;">{{ $sesion->reserva->paquete->nombre }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 20px;background-color:#ffffff;border-bottom:1px solid #e8e8e8;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Fecha de sesión</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">{{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d/m/Y') }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 20px;background-color:#fafafa;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Lugar</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">{{ $sesion->lugar }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    {{-- Banner próximo paso --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
        <tr>
            <td style="background-color:#fff3e8;border-left:4px solid #e87722;padding:14px 18px;border-radius:0 8px 8px 0;">
                <p style="margin:0;font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;line-height:1.6;">
                    <strong>¡A disfrutar!</strong> Ingresa a tu galería para descargar tus fotografías editadas.
                </p>
            </td>
        </tr>
    </table>

    {{-- CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:32px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="border-radius:12px;background-color:#e87722;">
                            <a href="{{ url('/cliente/galeria') }}"
                               target="_blank"
                               style="display:inline-block;padding:14px 32px;font-family:Arial,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:12px;">
                                Ver mi galería &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

@endsection
