@extends('layouts.email')

@section('email_title', 'Solicitud de Estudio Aprobada')

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
                    <i class="ti ti-check" style="font-size:24px;color:#e87722;"></i>
                </div>
            </td>
        </tr>
    </table>

    {{-- Título --}}
    <h1 style="margin:0 0 12px;font-family:Georgia,'Times New Roman',serif;font-size:26px;font-weight:700;color:#1a0d00;line-height:1.3;text-align:center;">
        Solicitud de renta de estudio aprobada
    </h1>

    {{-- Subtítulo --}}
    <p style="margin:0 0 28px;font-family:Arial,sans-serif;font-size:15px;line-height:1.7;color:#4b5563;text-align:center;">
        Hola <strong style="color:#1a0d00;">{{ $solicitud->nombre }} {{ $solicitud->apellido }}</strong>,
        tu solicitud para rentar el estudio ha sido aprobada. Aquí tienes el resumen y los detalles importantes.
    </p>

    {{-- Detalle de la reserva --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="background-color:#faf9f7;border:1px solid #e8e8e8;border-radius:10px;margin-bottom:28px;">
        <tr>
            <td style="padding:20px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="font-family:Arial,sans-serif;font-size:14px;color:#1a0d00;">
                    <tr>
                        <td style="padding:6px 0;color:#9ca3af;width:40%;">Fecha</td>
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
                    <tr>
                        <td style="padding:6px 0;color:#9ca3af;">Personas</td>
                        <td style="padding:6px 0;font-weight:600;">{{ $solicitud->cantidad_personas }}</td>
                    </tr>
                    <tr>
                        <td style="padding:6px 0;color:#9ca3af;">Iluminación</td>
                        <td style="padding:6px 0;font-weight:600;">{{ $iluminaciones[$solicitud->iluminacion] ?? $solicitud->iluminacion }}</td>
                    </tr>
                    @if($solicitud->color_fondo)
                        <tr>
                            <td style="padding:6px 0;color:#9ca3af;">Fondo</td>
                            <td style="padding:6px 0;font-weight:600;">{{ $solicitud->color_fondo }}{{ $solicitud->color_fondo_adicional ? ' + ' . $solicitud->color_fondo_adicional : '' }}</td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    {{-- Reglas del estudio --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:28px;">
        <tr>
            <td>
                <p style="margin:0 0 10px;font-family:Arial,sans-serif;font-size:13px;font-weight:700;color:#1a0d00;text-transform:uppercase;letter-spacing:0.5px;">
                    Reglas del estudio
                </p>
                <ul style="margin:0;padding-left:18px;font-family:Arial,sans-serif;font-size:14px;line-height:1.8;color:#4b5563;">
                    <li>Llega puntual; el tiempo excedido puede generar un cargo adicional.</li>
                    <li>El estudio debe entregarse en las mismas condiciones en que se recibió.</li>
                    <li>Cualquier daño a equipos, fondos o mobiliario corre por cuenta del cliente.</li>
                    <li>No se permite fumar ni consumir alimentos dentro del área de fotografía.</li>
                    <li>Cancelaciones con menos de 24 horas de anticipación pueden no ser reembolsables.</li>
                </ul>
            </td>
        </tr>
    </table>

    {{-- Divisor --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0" style="margin-bottom:24px;">
        <tr>
            <td style="height:1px;background-color:#e8e8e8;font-size:1px;line-height:1px;">&nbsp;</td>
        </tr>
    </table>

    {{-- Dirección --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
        <tr>
            <td align="center">
                <p style="margin:0 0 6px;font-family:Arial,sans-serif;font-size:13px;font-weight:700;color:#1a0d00;text-transform:uppercase;letter-spacing:0.5px;">
                    Ubicación del estudio
                </p>
                <p style="margin:0 0 4px;font-family:Arial,sans-serif;font-size:14px;color:#4b5563;">
                    Calle E. León Jiménez #24, 2do nivel del Café de la Abuela
                </p>
                <a href="https://maps.app.goo.gl/EUeYCm5CQjh7Fkf39" style="font-family:Arial,sans-serif;font-size:13px;color:#e87722;text-decoration:none;">
                    Ver ubicación en Google Maps →
                </a>
            </td>
        </tr>
    </table>

    <p style="margin:24px 0 0;font-family:Arial,sans-serif;font-size:12px;color:#9ca3af;text-align:center;line-height:1.6;">
        Si tienes alguna pregunta sobre tu reserva, contáctanos respondiendo a este correo.
    </p>
@endsection
