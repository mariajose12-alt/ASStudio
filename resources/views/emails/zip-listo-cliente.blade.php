@extends('layouts.email')

@section('email_title', 'Tus fotos están listas — AStudio')

@section('content')

    {{-- Ícono decorativo --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="margin-bottom:28px;">
        <tr>
            <td align="center">
                <div style="display:inline-block;width:64px;height:64px;border-radius:16px;
                            background-color:#fff3e8;border:1px solid rgba(232,119,34,0.25);
                            text-align:center;line-height:64px;font-size:28px;">
                    📦
                </div>
            </td>
        </tr>
    </table>

    {{-- Saludo --}}
    <p style="margin:0 0 8px;font-family:Georgia,'Times New Roman',serif;
              font-size:22px;font-weight:600;color:#1a0d00;line-height:1.3;text-align:center;">
        ¡Tu galería está lista!
    </p>

    <p style="margin:0 0 28px;font-family:Arial,sans-serif;font-size:14px;
              color:#6b6b6b;line-height:1.7;text-align:center;">
        Hola
        <strong style="color:#1a1a1a;">
            {{ $sesion->reserva->cliente->usuario->persona->nombre }}
        </strong>,
        hemos preparado tu paquete de fotos
        <strong style="color:#1a1a1a;">{{ $tipo === 'editadas' ? 'editadas' : 'originales' }}</strong>
        de la sesión del
        <strong style="color:#1a1a1a;">
            {{ \Carbon\Carbon::parse($sesion->fecha_inicio)->translatedFormat('j \d\e F \d\e Y') }}
        </strong>.
    </p>

    {{-- Tarjeta resumen de la sesión --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="background-color:#f9f9f9;border:1px solid #e8e8e8;border-radius:12px;
                  margin-bottom:32px;">
        <tr>
            <td style="padding:20px 24px;">
                <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">

                    {{-- Paquete --}}
                    <tr>
                        <td style="padding-bottom:12px;">
                            <span style="display:block;font-family:Arial,sans-serif;font-size:10px;
                                         font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
                                         color:#9ca3af;margin-bottom:3px;">Paquete</span>
                            <span style="font-family:Arial,sans-serif;font-size:13px;
                                         color:#1a1a1a;font-weight:500;">
                                {{ $sesion->reserva->paquete->nombre }}
                            </span>
                        </td>
                    </tr>

                    {{-- Fecha --}}
                    <tr>
                        <td style="padding-bottom:12px;">
                            <span style="display:block;font-family:Arial,sans-serif;font-size:10px;
                                         font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
                                         color:#9ca3af;margin-bottom:3px;">Fecha de sesión</span>
                            <span style="font-family:Arial,sans-serif;font-size:13px;
                                         color:#1a1a1a;font-weight:500;">
                                {{ \Carbon\Carbon::parse($sesion->fecha_inicio)->translatedFormat('l, j \d\e F \d\e Y') }}
                            </span>
                        </td>
                    </tr>

                    {{-- Tipo de descarga --}}
                    <tr>
                        <td>
                            <span style="display:block;font-family:Arial,sans-serif;font-size:10px;
                                         font-weight:700;letter-spacing:1.5px;text-transform:uppercase;
                                         color:#9ca3af;margin-bottom:3px;">Contenido</span>
                            <span style="font-family:Arial,sans-serif;font-size:13px;
                                         color:#1a1a1a;font-weight:500;">
                                Fotos {{ $tipo === 'editadas' ? 'editadas' : 'originales' }}
                                ({{ $sesion->fotografias->where('estado', $tipo === 'editadas' ? 'EDITADA' : 'ORIGINAL')->count() }} imágenes)
                            </span>
                        </td>
                    </tr>

                </table>
            </td>
        </tr>
    </table>

    {{-- CTA principal --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="margin-bottom:24px;">
        <tr>
            <td align="center">
                <a href="{{ $urlDescarga }}"
                   target="_blank"
                   style="display:inline-block;padding:14px 40px;background-color:#e87722;
                          border-radius:8px;font-family:Arial,sans-serif;font-size:15px;
                          font-weight:700;color:#ffffff;text-decoration:none;
                          letter-spacing:0.3px;">
                    Descargar mis fotos
                </a>
            </td>
        </tr>
    </table>

    {{-- Aviso de expiración --}}
    <p style="margin:0 0 28px;font-family:Arial,sans-serif;font-size:12px;
              color:#9ca3af;line-height:1.6;text-align:center;">
        ⏳ Este enlace estará disponible durante <strong>24 horas</strong>.<br>
        Pasado ese tiempo, puedes generar uno nuevo desde tu portal.
    </p>

    {{-- Divisor --}}
    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
           style="margin-bottom:24px;">
        <tr>
            <td style="height:1px;background-color:#e8e8e8;font-size:1px;line-height:1px;">&nbsp;</td>
        </tr>
    </table>

    {{-- Link alternativo por si el botón falla --}}
    <p style="margin:0;font-family:Arial,sans-serif;font-size:12px;
              color:#9ca3af;line-height:1.7;text-align:center;">
        ¿El botón no funciona? Copia y pega este enlace en tu navegador:<br>
        <a href="{{ $urlDescarga }}"
           style="color:#e87722;text-decoration:underline;word-break:break-all;font-size:11px;">
            {{ $urlDescarga }}
        </a>
    </p>

@endsection
