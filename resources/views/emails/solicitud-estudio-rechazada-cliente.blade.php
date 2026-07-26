@extends('layouts.email')

@section('email_title', 'Solicitud de Estudio Rechazada')

@section('content')

    @php
        $finalidades = [
            'fotografia' => 'Fotografía',
            'video' => 'Video',
            'podcast' => 'Podcast',
            'contenido_personal' => 'Contenido personal orgánico',
            'evento' => 'Evento',
        ];
        $iluminaciones = [
            'luz_fija' => 'Luz fija',
            'flashes' => 'Flashes',
            'luz_natural' => 'Luz natural',
            'luz_fija_flash' => 'Luz fija y flash',
            'otro' => $solicitud->iluminacion_otro ?? 'Otro',
        ];
    @endphp

    {{-- Ícono de estado --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center" style="padding-bottom:24px;">
                <div style="display:inline-block;width:56px;height:56px;border-radius:50%;background-color:#fdf0ee;border:2px solid rgba(200,60,40,0.25);text-align:center;line-height:56px;font-size:24px;">
                    ✕
                </div>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 12px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:700;color:#1a0d00;line-height:1.3;text-align:center;">
        Solicitud de renta de estudio rechazada
    </h1>

    {{-- Subtítulo --}}
    <p style="margin:0 0 28px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#4b5563;text-align:center;">
        Hola <strong style="color:#1a0d00;">{{ $solicitud->nombre }} {{ $solicitud->apellido }}</strong>,
        lamentamos informarte que tu solicitud para rentar el estudio no pudo ser aprobada.
    </p>

    {{-- Detalle de la solicitud --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#faf9f7;border:1px solid #e8e8e8;border-radius:10px;margin-bottom:24px;">
        <tr>
            <td style="padding:20px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;">
                    <tr>
                        <td style="padding:6px 0;color:#9ca3af;width:40%;">Fecha solicitada</td>
                        <td style="padding:6px 0;font-weight:600;">{{ \Carbon\Carbon::parse($solicitud->fecha)->translatedFormat('d \d\e F, Y') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#9ca3af;">Horario</td>
                        <td style="padding:6px 0;font-weight:600;">{{ \Carbon\Carbon::parse($solicitud->hora_inicio)->format('g:i A') }} – {{ \Carbon\Carbon::parse($solicitud->hora_fin)->format('g:i A') }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#9ca3af;">Finalidad</td>
                        <td style="padding:6px 0;font-weight:600;">{{ $finalidades[$solicitud->finalidad] ?? $solicitud->finalidad }}</td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($solicitud->nota_socio)
        {{-- Motivo del rechazo, si el socio dejó una nota --}}
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
            <tr>
                <td>
                    <p style="margin:0 0 8px;font-family:Arial,sans-serif;font-size:13px;font-weight:700;color:#1a0d00;text-transform:uppercase;letter-spacing:0.5px;">
                        Motivo
                    </p>
                    <p style="margin:0;font-family:Arial,sans-serif;font-size:14px;line-height:1.7;color:#4b5563;">
                        {{ $solicitud->nota_socio }}
                    </p>
                </td>
            </tr>
        </table>
    @endif

    {{-- Divisor --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <tr>
            <td style="height:1px;background-color:#e8e8e8;font-size:1px;line-height:1px;">&nbsp;</td>
        </tr>
    </table>

    <p style="margin:0 0 24px;font-family:Arial,sans-serif;font-size:14px;line-height:1.7;color:#4b5563;text-align:center;">
        Puedes enviar una nueva solicitud para otra fecha u horario disponible cuando gustes.
    </p>

    <p style="margin:0;font-family:Arial,sans-serif;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
        Si tienes alguna pregunta, contáctanos respondiendo a este correo.
    </p>
@endsection
