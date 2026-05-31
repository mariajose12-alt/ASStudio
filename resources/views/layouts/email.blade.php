<!DOCTYPE html>
<html lang="es" xmlns="http://www.w3.org/1999/xhtml" xmlns:v="urn:schemas-microsoft-com:vml" xmlns:o="urn:schemas-microsoft-com:office:office">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="x-apple-disable-message-reformatting">
    <meta name="format-detection" content="telephone=no,address=no,email=no,date=no,url=no">
    <title>@yield('email_title', config('app.name'))</title>

    <!--[if mso]>
    <noscript>
        <xml>
            <o:OfficeDocumentSettings>
                <o:PixelsPerInch>96</o:PixelsPerInch>
            </o:OfficeDocumentSettings>
        </xml>
    </noscript>
    <![endif]-->

    <style>
        /* ── Reset ── */
        * { box-sizing: border-box; }
        body, table, td, a { -webkit-text-size-adjust: 100%; -ms-text-size-adjust: 100%; }
        table, td { mso-table-lspace: 0pt; mso-table-rspace: 0pt; }
        img { -ms-interpolation-mode: bicubic; border: 0; outline: none; text-decoration: none; }
        body { margin: 0 !important; padding: 0 !important; width: 100% !important; }

        /* ── Fuentes ── */
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Playfair+Display:wght@400;600&display=swap');

        /* ── Variables de color ── */
        :root {
            --brand-primary:   #e87722;
            --brand-dark:      #1a0d00;
            --brand-accent:    #c96015;
            --brand-light:     #fff3e8;
            --brand-soft:      #f5c49a;
            --text-primary:    #1a1a1a;
            --text-secondary:  #6b6b6b;
            --text-muted:      #9ca3af;
            --bg-body:         #f4f4f5;
            --bg-card:         #ffffff;
            --bg-header:       #1a0d00;
            --border-color:    #e8e8e8;
            --success:         #059669;
            --success-bg:      #ecfdf5;
            --warning:         #d97706;
            --warning-bg:      #fffbeb;
            --danger:          #dc2626;
            --danger-bg:       #fef2f2;
            --info:            #2563eb;
            --info-bg:         #eff6ff;
        }

        /* ── Base ── */
        body {
            background-color: #f4f4f5;
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1a1a1a;
        }

        .email-wrapper {
            width: 100%;
            background-color: #f4f4f5;
            padding: 32px 16px;
        }

        .email-container {
            max-width: 600px;
            margin: 0 auto;
        }

        /* ── Header ── */
        .email-header {
            background: linear-gradient(135deg, #1a0d00 0%, #2d1500 60%, #3d1f00 100%);
            border-radius: 20px 20px 0 0;
            padding: 40px 48px 36px;
            text-align: center;
        }

        .email-header__logo {
            display: block;
            margin: 0 auto 24px;
            max-height: 48px;
            width: auto;
        }

        .email-header__divider {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-top: 8px;
        }

        .email-header__line {
            height: 1px;
            width: 60px;
            background: linear-gradient(90deg, transparent, rgba(232,119,34,0.6));
        }

        .email-header__line--right {
            background: linear-gradient(90deg, rgba(232,119,34,0.6), transparent);
        }

        .email-header__dot {
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: #e87722;
        }

        /* ── Card principal ── */
        .email-card {
            background: #ffffff;
            padding: 48px;
            border-left: 1px solid #e8e8e8;
            border-right: 1px solid #e8e8e8;
        }

        /* ── Info block ── */
        .email-info-block {
            background: #fff3e8;
            border: 1px solid rgba(232,119,34,0.2);
            border-radius: 14px;
            padding: 24px 28px;
            margin: 32px 0 0;
        }

        .email-info-block__title {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 15px;
            font-weight: 600;
            color: #1a0d00;
            margin: 0 0 16px;
            letter-spacing: 0.3px;
        }

        .email-info-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 12px;
        }

        .email-info-item {
            display: flex;
            flex-direction: column;
            gap: 2px;
        }

        .email-info-item__label {
            font-size: 10px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: #9ca3af;
        }

        .email-info-item__value {
            font-size: 13px;
            color: #1a1a1a;
            font-weight: 500;
        }

        .email-info-item__value a {
            color: #e87722;
            text-decoration: none;
        }

        .email-company-info {
            margin-top: 40px;
            padding-top: 24px;
            border-top: 1px solid #e8e8e8;
        }

        /* ── Footer ── */
        .email-footer {
            background: #111111;
            border-radius: 0 0 20px 20px;
            padding: 36px 48px 32px;
            text-align: center;
            border: 1px solid #222;
            border-top: none;
        }

        .email-footer__social {
            display: flex;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }

        .email-footer__social-link {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            width: 38px;
            height: 38px;
            border-radius: 10px;
            background: rgba(255,255,255,0.08);
            text-decoration: none;
            transition: background 0.2s;
        }

        .email-footer__social-link:hover {
            background: rgba(232,119,34,0.2);
        }

        .email-footer__social-link img,
        .email-footer__social-link svg {
            width: 18px;
            height: 18px;
        }

        .email-footer__contact {
            font-size: 13px;
            color: #9ca3af;
            line-height: 1.8;
            margin-bottom: 20px;
        }

        .email-footer__contact a {
            color: #e87722;
            text-decoration: none;
        }

        .email-footer__divider {
            border: none;
            border-top: 1px solid rgba(255,255,255,0.08);
            margin: 20px 0;
        }

        .email-footer__legal {
            font-size: 11px;
            color: #6b6b6b;
            line-height: 1.7;
        }

        .email-footer__legal a {
            color: #9ca3af;
            text-decoration: underline;
        }

        /* ── Sombra exterior ── */
        .email-shadow {
            box-shadow: 0 4px 6px -1px rgba(0,0,0,0.07), 0 20px 60px -10px rgba(0,0,0,0.12);
            border-radius: 20px;
        }

        /* ── Responsive ── */
        @media (max-width: 620px) {
            .email-wrapper { padding: 16px 12px; }
            .email-header  { padding: 28px 24px 24px; border-radius: 16px 16px 0 0; }
            .email-card    { padding: 28px 24px; }
            .email-footer  { padding: 28px 24px 24px; border-radius: 0 0 16px 16px; }
            .email-info-grid { grid-template-columns: 1fr; }
            .email-info-block { padding: 20px; }
        }
    </style>
</head>
<body>
<div class="email-wrapper">
    <div class="email-container email-shadow">

        {{-- ══ HEADER ══ --}}
        <div class="email-header">
            <img src="{{ isset($message) ? $message->embed(public_path('images/logo.png')) : asset('images/logo.png') }}"
                 alt="AStudio"
                 class="email-header__logo"
                 style="display:block; margin:0 auto 24px; max-height:48px; width:auto; filter: invert(1);">

            <div class="email-header__divider">
                <div class="email-header__line"   style="height:1px;width:60px;background:linear-gradient(90deg,transparent,rgba(232,119,34,0.6));"></div>
                <div class="email-header__dot"    style="width:6px;height:6px;border-radius:50%;background:#e87722;"></div>
                <div class="email-header__line email-header__line--right" style="height:1px;width:60px;background:linear-gradient(90deg,rgba(232,119,34,0.6),transparent);"></div>
            </div>
        </div>

        {{-- ══ CARD PRINCIPAL ══ --}}
        <div class="email-card">
            @yield('content')

            {{-- ══ BLOQUE INFO EMPRESA ══ --}}
            <div class="email-company-info"
                @unless(View::hasSection('hide_info'))
                    <div class="email-info-block">
                        <p class="email-info-block__title">AStudio</p>
                        <div class="email-info-grid">
                            <div class="email-info-item">
                                <span class="email-info-item__label">Correo</span>
                                <span class="email-info-item__value">
                            <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
                        </span>
                            </div>
                            <div class="email-info-item">
                                <span class="email-info-item__label">Teléfono</span>
                                <span class="email-info-item__value">
                            <a href="tel:{{ config('company.phone', '+1 000 000 0000') }}">{{ config('company.phone', '+1 000 000 0000') }}</a>
                        </span>
                            </div>
                            <div class="email-info-item">
                                <span class="email-info-item__label">Horario</span>
                                <span class="email-info-item__value">{{ config('company.hours', 'Lun – Sáb, 9am – 7pm') }}</span>
                            </div>
                            <div class="email-info-item">
                                <span class="email-info-item__label">Sitio web</span>
                                <span class="email-info-item__value">
                            <a href="{{ config('app.url') }}" target="_blank">{{ str_replace(['https://','http://'], '', config('app.url')) }}</a>
                        </span>
                            </div>
                        </div>
                    </div>
                @endunless
            </div>
        {{-- ══ FOOTER ══ --}}
        <div class="email-footer">

            {{-- Redes sociales --}}
            <div class="email-footer__social">
                @if(config('company.social.instagram'))
                    <a href="{{ config('company.social.instagram') }}" class="email-footer__social-link" target="_blank" title="Instagram"
                       style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.08);text-decoration:none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <rect x="2" y="2" width="20" height="20" rx="5" stroke="#9ca3af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37Z" stroke="#9ca3af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                            <line x1="17.5" y1="6.5" x2="17.51" y2="6.5" stroke="#9ca3af" stroke-width="2" stroke-linecap="round"/>
                        </svg>
                    </a>
                @endif

                @if(config('company.social.whatsapp'))
                    <a href="https://wa.me/{{ preg_replace('/\D/', '', config('company.social.whatsapp')) }}" class="email-footer__social-link" target="_blank" title="WhatsApp"
                       style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.08);text-decoration:none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M21 11.5a8.38 8.38 0 0 1-.9 3.8 8.5 8.5 0 0 1-7.6 4.7 8.38 8.38 0 0 1-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 0 1-.9-3.8 8.5 8.5 0 0 1 4.7-7.6 8.38 8.38 0 0 1 3.8-.9h.5a8.48 8.48 0 0 1 8 8v.5Z" stroke="#9ca3af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endif

                @if(config('company.social.tiktok'))
                    <a href="{{ config('company.social.tiktok') }}" class="email-footer__social-link" target="_blank" title="TikTok"
                       style="display:inline-flex;align-items:center;justify-content:center;width:38px;height:38px;border-radius:10px;background:rgba(255,255,255,0.08);text-decoration:none;">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M9 12a4 4 0 1 0 4 4V4a5 5 0 0 0 5 5" stroke="#9ca3af" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/>
                        </svg>
                    </a>
                @endif
            </div>

            {{-- Contacto --}}
            <div class="email-footer__contact">
                <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
                &nbsp;·&nbsp;
                <a href="tel:{{ config('company.phone', '') }}">{{ config('company.phone', '') }}</a>
                @if(config('company.address'))
                    <br>{{ config('company.address') }}
                @endif
            </div>

            <hr class="email-footer__divider" style="border:none;border-top:1px solid rgba(255,255,255,0.08);margin:20px 0;">

            {{-- Legal --}}
            <div class="email-footer__legal">
                Este correo fue enviado automáticamente, por favor no respondas a este mensaje.<br>
                Si tienes dudas, contáctanos en
                <a href="mailto:{{ config('mail.from.address') }}">{{ config('mail.from.address') }}</a>
                o visita nuestro
                <a href="{{ config('app.url') }}/soporte" target="_blank">centro de soporte</a>.<br><br>
                &copy; {{ date('Y') }} AStudio. Todos los derechos reservados.
            </div>
        </div>
    </div>
</div>
</body>
</html>
