<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@latest/dist/tabler-icons.min.css">
    <title>@yield('email_title', config('app.name'))</title>
    <!--[if mso]>
    <noscript><xml><o:OfficeDocumentSettings><o:PixelsPerInch>96</o:PixelsPerInch></o:OfficeDocumentSettings></xml></noscript>
    <![endif]-->
    {{-- Solo estilos que Gmail respeta: reset básico y media queries para móvil --}}
    <style type="text/css">
        body, table, td, p, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; border-collapse: collapse; }
        img { -ms-interpolation-mode: bicubic; border: 0; display: block; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; background-color: #f2f2f2; }
        a { color: #e87722; text-decoration: none; }
        @media screen and (max-width: 620px) {
            .email-container { width: 100% !important; }
            .mobile-padding  { padding: 28px 20px !important; }
            .mobile-full     { width: 100% !important; display: block !important; }
            .mobile-hide     { display: none !important; }
        }
    </style>
</head>
<body style="margin:0;padding:0;background-color:#f2f2f2;">

{{-- Wrapper externo --}}
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
       style="background-color:#f2f2f2;">
    <tr>
        <td align="center" style="padding:32px 16px;">

            {{-- Contenedor de 600px --}}
            <table role="presentation" class="email-container" width="600" cellpadding="0" cellspacing="0" border="0"
                   style="width:600px;max-width:600px;">

                {{-- ══ HEADER ══ --}}
                <tr>
                    <td style="background-color:#1a0d00;border-radius:16px 16px 0 0;padding:36px 48px 28px;text-align:center;">

                        {{-- Logo --}}
                        <img src="{{ isset($message) ? $message->embed(public_path('images/logo.png')) : asset('images/logo.png') }}"
                             alt="Abraham Sánchez"
                             width="140"
                             style="display:block;margin:0 auto 20px;max-height:48px;width:auto;filter:brightness(0) invert(1);">

                        {{-- Línea decorativa: tabla de 3 celdas --}}
                        <table role="presentation" width="160" cellpadding="0" cellspacing="0" border="0"
                               style="margin:0 auto;">
                            <tr>
                                <td width="60" style="height:1px;background-color:rgba(232,119,34,0.5);font-size:1px;line-height:1px;">&nbsp;</td>
                                <td width="10" align="center" style="padding:0 6px;">
                                    <div style="width:6px;height:6px;border-radius:50%;background-color:#e87722;margin:0 auto;font-size:1px;line-height:1px;">&nbsp;</div>
                                </td>
                                <td width="60" style="height:1px;background-color:rgba(232,119,34,0.5);font-size:1px;line-height:1px;">&nbsp;</td>
                            </tr>
                        </table>

                    </td>
                </tr>

                {{-- ══ CARD PRINCIPAL ══ --}}
                <tr>
                    <td class="mobile-padding"
                        style="background-color:#ffffff;padding:44px 48px;border-left:1px solid #e8e8e8;border-right:1px solid #e8e8e8;">

                        @yield('content')

                        {{-- ══ BLOQUE INFO EMPRESA ══ --}}
                        @unless(View::hasSection('hide_company_info'))
                            <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                   style="margin-top:36px;border-top:1px solid #e8e8e8;">
                                <tr>
                                    <td style="padding-top:28px;">
                                        {{-- Título del bloque --}}
                                        <p style="margin:0 0 16px;font-family:Georgia,'Times New Roman',serif;font-size:15px;font-weight:600;color:#1a0d00;letter-spacing:0.3px;">
                                            Abraham Sánchez
                                        </p>
                                        {{-- Grid de 2 columnas con tabla --}}
                                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0"
                                               style="background-color:#fff3e8;border:1px solid rgba(232,119,34,0.2);border-radius:10px;">
                                            <tr>
                                                <td style="padding:20px 24px;">
                                                    <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                                                        <tr>
                                                            {{-- Columna izquierda --}}
                                                            <td class="mobile-full" width="50%" valign="top"
                                                                style="padding-right:12px;">
                                                                <p style="margin:0 0 12px;font-family:Arial,sans-serif;">
                                                                    <span style="display:block;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin-bottom:3px;">Correo</span>
                                                                    <a href="mailto:{{ config('mail.from.address') }}"
                                                                       style="font-size:13px;color:#e87722;text-decoration:none;font-weight:500;">{{ config('mail.from.address') }}</a>
                                                                </p>
                                                                <p style="margin:0;font-family:Arial,sans-serif;">
                                                                    <span style="display:block;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin-bottom:3px;">Horario</span>
                                                                    <span style="font-size:13px;color:#1a1a1a;font-weight:500;">{{ config('company.hours', 'Lun – Sáb, 9am – 7pm') }}</span>
                                                                </p>
                                                            </td>
                                                            {{-- Columna derecha --}}
                                                            <td class="mobile-full" width="50%" valign="top"
                                                                style="padding-left:12px;">
                                                                <p style="margin:0 0 12px;font-family:Arial,sans-serif;">
                                                                    <span style="display:block;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin-bottom:3px;">Teléfono</span>
                                                                    <a href="tel:{{ config('company.phone', '') }}"
                                                                       style="font-size:13px;color:#e87722;text-decoration:none;font-weight:500;">{{ config('company.phone', '+1 000 000 0000') }}</a>
                                                                </p>
                                                                <p style="margin:0;font-family:Arial,sans-serif;">
                                                                    <span style="display:block;font-size:10px;font-weight:700;letter-spacing:1.5px;text-transform:uppercase;color:#9ca3af;margin-bottom:3px;">Sitio web</span>
                                                                    <a href="{{ config('app.url') }}" target="_blank"
                                                                       style="font-size:13px;color:#e87722;text-decoration:none;font-weight:500;">{{ str_replace(['https://','http://'], '', config('app.url')) }}</a>
                                                                </p>
                                                            </td>
                                                        </tr>
                                                    </table>
                                                </td>
                                            </tr>
                                        </table>
                                    </td>
                                </tr>
                            </table>
                        @endunless

                    </td>
                </tr>

                {{-- ══ FOOTER ══ --}}
                <tr>
                    <td class="mobile-padding"
                        style="background-color:#111111;border-radius:0 0 16px 16px;padding:32px 48px 28px;text-align:center;border:1px solid #222222;border-top:none;">

                        {{-- Redes sociales --}}
                        @if(config('company.social.instagram') || config('company.social.whatsapp') || config('company.social.tiktok'))
                            <table role="presentation" cellpadding="0" cellspacing="0" border="0"
                                   style="margin:0 auto 24px;">
                                <tr>
                                    @if(config('company.social.instagram'))
                                        <td style="padding:0 5px;">
                                            <a href="{{ config('company.social.instagram') }}" target="_blank"
                                               style="display:inline-block;width:38px;height:38px;border-radius:10px;background-color:rgba(255,255,255,0.08);text-decoration:none;text-align:center;line-height:38px;">
                                                <img src="{{ $message->embed(public_path('images/email/icon-instagram.png')) }}" width="18" height="18"
                                                     alt="Instagram" style="display:inline-block;vertical-align:middle;">
                                            </a>
                                        </td>
                                    @endif
                                    @if(config('company.social.whatsapp'))
                                        <td style="padding:0 5px;">
                                            <a href="https://wa.me/{{ preg_replace('/\D/', '', config('company.social.whatsapp')) }}" target="_blank"
                                               style="display:inline-block;width:38px;height:38px;border-radius:10px;background-color:rgba(255,255,255,0.08);text-decoration:none;text-align:center;line-height:38px;">
                                                <img src="{{ $message->embed(public_path('images/email/icon-whatsapp.png')) }}" width="18" height="18"
                                                     alt="WhatsApp" style="display:inline-block;vertical-align:middle;">
                                            </a>
                                        </td>
                                    @endif
                                    @if(config('company.social.tiktok'))
                                        <td style="padding:0 5px;">
                                            <a href="{{ config('company.social.tiktok') }}" target="_blank"
                                               style="display:inline-block;width:38px;height:38px;border-radius:10px;background-color:rgba(255,255,255,0.08);text-decoration:none;text-align:center;line-height:38px;">
                                                <img src="{{ $message->embed(public_path('images/email/icon-tiktok.png')) }}" width="18" height="18"
                                                     alt="TikTok" style="display:inline-block;vertical-align:middle;">
                                            </a>
                                        </td>
                                    @endif
                                </tr>
                            </table>
                        @endif

                        {{-- Contacto --}}
                        <p style="margin:0 0 20px;font-family:Arial,sans-serif;font-size:13px;color:#9ca3af;line-height:1.8;">
                            <a href="mailto:{{ config('mail.from.address') }}"
                               style="color:#e87722;text-decoration:none;">{{ config('mail.from.address') }}</a>
                            &nbsp;·&nbsp;
                            <a href="tel:{{ config('company.phone', '') }}"
                               style="color:#e87722;text-decoration:none;">{{ config('company.phone', '') }}</a>
                            @if(config('company.address'))
                                <br>{{ config('company.address') }}
                            @endif
                        </p>

                        {{-- Divisor --}}
                        <table role="presentation" width="100%" cellpadding="0" cellspacing="0" border="0">
                            <tr>
                                <td style="height:1px;background-color:rgba(255,255,255,0.08);font-size:1px;line-height:1px;margin:0 0 20px;">&nbsp;</td>
                            </tr>
                        </table>

                        {{-- Legal --}}
                        <p style="margin:16px 0 0;font-family:Arial,sans-serif;font-size:11px;color:#6b6b6b;line-height:1.7;">
                            Este correo fue enviado automáticamente, por favor no respondas a este mensaje.<br>
                            Si tienes dudas, contáctanos en
                            <a href="mailto:{{ config('mail.from.address') }}"
                               style="color:#9ca3af;text-decoration:underline;">{{ config('mail.from.address') }}</a>
                            o visita nuestro
                            <a href="{{ config('app.url') }}/soporte" target="_blank"
                               style="color:#9ca3af;text-decoration:underline;">centro de soporte</a>.<br><br>
                            &copy; {{ date('Y') }} Abraham Sánchez. Todos los derechos reservados.
                        </p>

                    </td>
                </tr>

            </table>
            {{-- /contenedor 600px --}}

        </td>
    </tr>
</table>

</body>
</html>
