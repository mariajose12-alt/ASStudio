@extends('layouts.email')

@section('email_title', 'Nueva Solicitud de Estudio')

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
                <div style="display:inline-block;width:56px;height:56px;border-radius:50%;background-color:#fff3e8;border:2px solid rgba(232,119,34,0.3);text-align:center;line-height:56px;font-size:24px;">
                    📅
                </div>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 12px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:700;color:#1a0d00;line-height:1.3;text-align:center;">
        Nueva solicitud de renta de estudio
    </h1>

    {{-- Subtítulo --}}
    <p style="margin:0 0 32px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#4b5563;text-align:center;">
        Hola <strong style="color:#1a0d00;">{{ $socio->nombre ?? '' }}</strong>,
        <strong style="color:#1a0d00;">{{ $solicitud->nombre }} {{ $solicitud->apellido }}</strong>
        solicitó rentar el estudio. Revísala y confírmala antes de que expire.
    </p>

    {{-- Divisor --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
        <tr>
            <td style="height:1px;background-color:#e8e8e8;font-size:1px;line-height:1px;">&nbsp;</td>
        </tr>
    </table>

    {{-- Etiqueta sección --}}
    <p style="margin:0 0 14px;font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;">
        Detalles de la solicitud
    </p>

    {{-- Tabla de detalles --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="border:1px solid #e8e8e8;border-radius:10px;overflow:hidden;">
        <tr>
            <td style="padding:14px 20px;background-color:#fafafa;border-bottom:1px solid #e8e8e8;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Cliente</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:600;">{{ $solicitud->nombre }} {{ $solicitud->apellido }}</span><br>
                            <span style="font-family:Arial,sans-serif;font-size:12px;color:#6b7280;">{{ $solicitud->email }} · {{ $solicitud->telefono }}</span>
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
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Fecha</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">
                                {{ $solicitud->fecha->format('d/m/Y') }} · {{ $solicitud->hora_inicio->format('H:i') }} - {{ $solicitud->hora_fin->format('H:i') }}
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 20px;background-color:#fafafa;border-bottom:1px solid #e8e8e8;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Finalidad</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">{{ $finalidades[$solicitud->finalidad] ?? $solicitud->finalidad }}</span>
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
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Personas</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">{{ $solicitud->cantidad_personas }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        <tr>
            <td style="padding:14px 20px;background-color:#fafafa;border-bottom:1px solid #e8e8e8;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Iluminación</span>
                        </td>
                        <td valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">{{ $iluminaciones[$solicitud->iluminacion] ?? $solicitud->iluminacion }}</span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
        @if($solicitud->color_fondo_adicional)
            <tr>
                <td style="padding:14px 20px;background-color:#ffffff;border-bottom:1px solid #e8e8e8;">
                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                        <tr>
                            <td width="140" valign="top">
                                <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Fondo</span>
                            </td>
                            <td valign="top">
                                <span style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;font-weight:500;">{{ $solicitud->color_fondo }}</span>
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        @endif
        <tr>
            <td style="padding:14px 20px;background-color:{{ $solicitud->color_fondo_adicional ? '#fafafa' : '#ffffff' }};">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="140" valign="top">
                            <span style="font-family:Arial,sans-serif;font-size:11px;font-weight:700;letter-spacing:1px;text-transform:uppercase;color:#9ca3af;">Estado</span>
                        </td>
                        <td valign="top">
                            {{-- Badge de estado --}}
                            <span style="display:inline-block;padding:3px 12px;background-color:#fff3e8;border-radius:20px;font-family:Arial,sans-serif;font-size:12px;font-weight:700;color:#e87722;letter-spacing:0.5px;">
                                Pendiente
                            </span>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    @if($solicitud->invitados)
        {{-- Banner de invitados --}}
        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
            <tr>
                <td style="background-color:#fff3e8;border-left:4px solid #e87722;padding:14px 18px;border-radius:0 8px 8px 0;">
                    <p style="margin:0;font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;line-height:1.6;">
                        <strong>Invitados adicionales:</strong> {{ $solicitud->invitados }}
                    </p>
                </td>
            </tr>
        </table>
    @endif

    {{-- CTA --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-top:24px;">
        <tr>
            <td align="center">
                <table role="presentation" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td style="border-radius:12px;background-color:#e87722;">
                            <a href="{{ route('admin.estudio.solicitudes.show', $solicitud->id) }}"
                               target="_blank"
                               style="display:inline-block;padding:14px 32px;font-family:Arial,sans-serif;font-size:15px;font-weight:700;color:#ffffff;text-decoration:none;border-radius:12px;">
                                Revisar solicitud →
                            </a>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>

    <p style="margin:20px 0 0;font-family:Arial,sans-serif;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
        Aprueba o rechaza desde el panel del estudio. El horario se reserva solo al aprobar.
    </p>
@endsection
