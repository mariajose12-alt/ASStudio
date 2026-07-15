@extends('layouts.email')

@section('email_title', 'Ayudante Confirmado')

@section('content')

    {{-- Ícono de estado --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:24px;">
                <div style="display:inline-block;width:56px;height:56px;border-radius:50%;background-color:#e8f5e9;border:2px solid rgba(46,125,50,0.3);text-align:center;line-height:56px;font-size:24px;">
                    ✓
                </div>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 12px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:700;color:#1a0d00;line-height:1.3;text-align:center;">
        ¡Fuiste confirmado como ayudante!
    </h1>

    {{-- Subtítulo --}}
    <p style="margin:0 0 32px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#4b5563;text-align:center;">
        Hola <strong style="color:#1a0d00;">{{ $postulacion->fotografo->getPersona()->nombre }}</strong>,
        <strong style="color:#1a0d00;">{{ $postulacion->solicitud->solicitante->getPersona()->nombre }}</strong>
        te confirmó para participar como asistente en su sesión.
    </p>

    {{-- Banner siguiente paso --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:8px;">
        <tr>
            <td style="background-color:#fff3e8;border-left:4px solid #e87722;padding:14px 18px;border-radius:0 8px 8px 0;">
                <p style="margin:0;font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;line-height:1.6;">
                    <strong>Siguiente paso:</strong> Encontrarás todos los detalles de la sesión (fecha, hora, lugar y cliente) en tu perfil dentro del sistema.
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
                            <a href="{{ route('fotografo.sesiones.index') }}"
                               target="_blank"
                               style="display:inline-block;padding:14px 32px;font-family:Arial,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:12px;">
                                Ver detalle completo &rarr;
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

@endsection
