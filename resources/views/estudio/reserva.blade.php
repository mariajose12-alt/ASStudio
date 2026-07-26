<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <title>Reserva el estudio — Zehcnas Studio</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,500;0,600;1,400&family=DM+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --sage: #a8b5a0;
            --blue-mid: #5b7c99;
            --ink: #1a1a1a;
            --navy: #1a2332;
            --snow: #faf9f6;
            --accent: #e87722;
            --accent-deep: #c5601a;
            --muted: #8a8478;
            --border: #e5e1d8;
        }
        * { box-sizing: border-box; }
        html, body {
            margin: 0;
            padding: 0;
            font-family: 'DM Sans', Arial, sans-serif;
            color: var(--ink);
            background: var(--snow);
            -webkit-font-smoothing: antialiased;
        }
        @media (prefers-reduced-motion: reduce) {
            * { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
        }

        /* ═══════════ SHELL ═══════════ */
        .rq-shell {
            display: flex;
            min-height: 100vh;
        }

        /* ═══════════ TICKET COLUMN (left) ═══════════ */
        .rq-ticket-col {
            position: sticky;
            top: 0;
            height: 100vh;
            width: 38%;
            min-width: 380px;
            max-width: 520px;
            background: linear-gradient(180deg, rgba(26,35,50,0.88), rgba(26,35,50,0.94)),
            url('{{ asset("images/medium/studio-interior.webp") }}') center/cover;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 44px 40px;
        }
        .rq-logo {
            height: 34px;
            filter: brightness(0) invert(1);
        }
        .rq-tagline {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 20px;
            color: rgba(255,255,255,0.82);
            line-height: 1.5;
            margin-top: 28px;
            max-width: 300px;
        }

        /* ── Ticket ── */
        .rq-ticket {
            background: var(--snow);
            border-radius: 4px;
            position: relative;
            padding: 26px 26px 22px;
            box-shadow: 0 24px 48px rgba(0,0,0,0.35);
        }
        .rq-ticket::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 5px;
            background: repeating-linear-gradient(90deg, var(--accent) 0 14px, transparent 14px 20px);
            border-radius: 4px 4px 0 0;
        }
        .rq-ticket-perf {
            position: relative;
            height: 0;
            margin: 18px -26px;
            border-top: 2px dashed var(--border);
        }
        .rq-ticket-perf::before, .rq-ticket-perf::after {
            content: "";
            position: absolute;
            top: -9px;
            width: 18px; height: 18px;
            background: var(--navy);
            border-radius: 50%;
        }
        .rq-ticket-perf::before { left: -9px; }
        .rq-ticket-perf::after { right: -9px; }

        .rq-ticket-head {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 4px;
        }
        .rq-ticket-title {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            letter-spacing: 0.3px;
            color: var(--navy);
        }
        .rq-ticket-num {
            font-size: 10px;
            color: var(--muted);
            letter-spacing: 1px;
            text-transform: uppercase;
            margin-top: 2px;
        }
        .rq-ticket-stamp {
            font-size: 10px;
            font-weight: 700;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            color: var(--accent);
            border: 1.5px solid var(--accent);
            border-radius: 999px;
            padding: 4px 10px;
            transform: rotate(3deg);
            white-space: nowrap;
        }
        .rq-ticket-stamp.rq-stamp-done {
            color: #0a7a4c;
            border-color: #0a7a4c;
        }

        .rq-ticket-rows { margin-top: 14px; }
        .rq-ticket-row {
            display: flex;
            justify-content: space-between;
            gap: 10px;
            padding: 9px 0;
            border-bottom: 1px solid var(--border);
            font-size: 13px;
        }
        .rq-ticket-row:last-child { border-bottom: none; }
        .rq-ticket-row-label {
            color: var(--muted);
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            padding-top: 2px;
        }
        .rq-ticket-row-value {
            text-align: right;
            font-weight: 500;
            color: var(--ink);
            font-variant-numeric: tabular-nums;
        }
        .rq-ticket-row-value.rq-empty {
            color: #c9c3b6;
            font-weight: 400;
        }

        .rq-ticket-foot {
            margin-top: 16px;
            font-size: 11px;
            color: var(--muted);
            line-height: 1.5;
        }

        .rq-col-bottom-note {
            font-size: 12px;
            color: rgba(255,255,255,0.55);
            line-height: 1.6;
        }
        .rq-col-bottom-note a { color: rgba(255,255,255,0.85); }

        /* ═══════════ FORM COLUMN (right) ═══════════ */
        .rq-form-col {
            flex: 1;
            min-width: 0;
            padding: 56px clamp(24px, 6vw, 88px);
            display: flex;
            justify-content: center;
        }
        .rq-form-inner {
            width: 100%;
            max-width: 520px;
        }
        .rq-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 13px;
            color: var(--muted);
            text-decoration: none;
            margin-bottom: 28px;
        }
        .rq-back:hover { color: var(--accent); }

        .rq-eyebrow {
            font-size: 11px;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            color: var(--accent);
            margin-bottom: 10px;
        }
        .rq-heading {
            font-family: 'Playfair Display', serif;
            font-size: clamp(28px, 4vw, 36px);
            font-weight: 500;
            color: var(--navy);
            margin: 0 0 10px;
            line-height: 1.2;
        }
        .rq-heading em { font-style: italic; color: var(--accent); }
        .rq-subtext {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.6;
            margin: 0 0 32px;
        }
        .rq-subtext a { color: var(--accent-deep); }

        /* ── Stepper ── */
        .rq-stepper {
            display: flex;
            align-items: center;
            margin-bottom: 36px;
        }
        .rq-step-node { display: flex; align-items: center; gap: 8px; }
        .rq-step-circle {
            width: 26px; height: 26px;
            border-radius: 50%;
            display: flex; align-items: center; justify-content: center;
            font-size: 11px; font-weight: 600;
            background: #fff;
            border: 1.5px solid var(--border);
            color: var(--muted);
            transition: all 0.25s ease;
        }
        .rq-step-node.active .rq-step-circle {
            background: var(--accent);
            border-color: var(--accent);
            color: #fff;
        }
        .rq-step-node.done .rq-step-circle {
            background: var(--navy);
            border-color: var(--navy);
            color: #fff;
        }
        .rq-step-label {
            font-size: 11px;
            color: var(--muted);
            display: none;
        }
        .rq-step-node.active .rq-step-label { color: var(--navy); font-weight: 600; }
        .rq-step-line {
            flex: 1;
            height: 1.5px;
            background: var(--border);
            margin: 0 6px;
        }
        .rq-step-line.done { background: var(--navy); }
        @media (min-width: 560px) {
            .rq-step-label { display: inline; }
        }

        /* ── Fields ── */
        .rq-field { margin-bottom: 20px; }
        .rq-field label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: var(--navy);
            margin-bottom: 8px;
        }
        .rq-field input[type="date"],
        .rq-field input[type="text"],
        .rq-field input[type="email"],
        .rq-field input[type="tel"],
        .rq-field input[type="number"],
        .rq-field select,
        .rq-field textarea {
            width: 100%;
            padding: 13px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            background: #fff;
            transition: border-color 0.15s ease;
            appearance: none;
            -webkit-appearance: none;
        }
        .rq-field select {
            background-image: url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24' fill='none' stroke='%238a8478' stroke-width='2'%3e%3cpolyline points='6 9 12 15 18 9'/%3e%3c/svg%3e");
            background-repeat: no-repeat;
            background-position: right 14px center;
            background-size: 16px;
            cursor: pointer;
        }
        .rq-field select:disabled {
            color: var(--muted);
            cursor: default;
            background-color: #faf9f6;
        }
        .rq-field input:focus, .rq-field select:focus, .rq-field textarea:focus {
            outline: none;
            border-color: var(--accent);
        }
        .rq-field input.rq-invalid, .rq-field select.rq-invalid, .rq-field textarea.rq-invalid {
            border-color: #d9302a;
        }
        .rq-row-2 {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }
        .rq-error {
            font-size: 12px;
            color: #c5341f;
            margin-top: 6px;
            display: none;
        }
        .rq-error.rq-show { display: block; }

        .rq-radio-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }
        .rq-radio-option {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 12px 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            font-size: 14px;
            cursor: pointer;
            transition: all 0.15s ease;
        }
        .rq-radio-option:has(input:checked) {
            border-color: var(--accent);
            background: #fff3e8;
        }
        .rq-radio-option input { accent-color: var(--accent); }

        .rq-check-row {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .rq-check-row input { accent-color: var(--accent); width: 16px; height: 16px; }

        .rq-availability {
            background: #fff3e8;
            border: 1px solid rgba(232,119,34,0.25);
            border-radius: 10px;
            padding: 12px 14px;
            margin-bottom: 20px;
            font-size: 12px;
        }
        .rq-availability-label { color: var(--accent-deep); font-weight: 600; margin-bottom: 8px; }
        .rq-chips { display: flex; flex-wrap: wrap; gap: 6px; }
        .rq-chip {
            background: #fff;
            border: 1px solid rgba(232,119,34,0.3);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: 11px;
            color: var(--accent-deep);
        }

        .rq-nav {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: 32px;
        }
        .rq-nav-end { justify-content: flex-end; }
        .rq-btn {
            background: var(--accent);
            color: #fff;
            border: none;
            padding: 14px 30px;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.2px;
            cursor: pointer;
            transition: background 0.15s ease;
        }
        .rq-btn:hover { background: var(--accent-deep); }
        .rq-btn:disabled { opacity: 0.6; cursor: default; }
        .rq-btn-ghost {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 13px;
            cursor: pointer;
            padding: 14px 6px;
        }
        .rq-btn-ghost:hover { color: var(--navy); }

        .rq-general-error {
            background: #fdecea;
            border: 1px solid rgba(217,48,37,0.25);
            color: #c5341f;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: none;
        }
        .rq-general-error.rq-show { display: block; }

        /* ── Paso 4 confirmación ── */
        .rq-confirm {
            text-align: center;
            padding: 20px 0;
        }
        .rq-confirm-icon {
            width: 64px; height: 64px;
            margin: 0 auto 22px;
            border-radius: 50%;
            background: #eafaf1;
            display: flex; align-items: center; justify-content: center;
            color: #0a7a4c;
        }
        .rq-confirm h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 500;
            color: var(--navy);
            margin: 0 0 12px;
        }
        .rq-confirm p {
            font-size: 14px;
            color: var(--muted);
            line-height: 1.7;
            margin: 0 auto 8px;
            max-width: 380px;
        }

        .rq-step[hidden] { display: none; }

        /* ═══════════ MOBILE ═══════════ */
        .rq-mobile-toggle { display: none; }

        @media (max-width: 959px) {
            .rq-shell { flex-direction: column; }

            .rq-ticket-col {
                position: static;
                width: 100%;
                min-width: 0;
                max-width: none;
                height: auto;
                padding: 22px 20px;
            }
            .rq-tagline { display: none; }
            .rq-col-bottom-note { display: none; }

            .rq-mobile-toggle {
                display: flex;
                align-items: center;
                justify-content: space-between;
                width: 100%;
                background: rgba(255,255,255,0.08);
                border: 1px solid rgba(255,255,255,0.15);
                border-radius: 12px;
                padding: 12px 16px;
                margin-top: 18px;
                color: #fff;
                font-size: 13px;
                cursor: pointer;
            }
            .rq-mobile-toggle svg { transition: transform 0.2s ease; }
            .rq-mobile-toggle.rq-open svg { transform: rotate(180deg); }

            .rq-ticket-wrap {
                max-height: 0;
                overflow: hidden;
                transition: max-height 0.3s ease;
            }
            .rq-ticket-wrap.rq-open {
                max-height: 600px;
                margin-top: 16px;
            }

            .rq-form-col { padding: 32px 20px 60px; }
        }
        /* ═══════════ MODAL REGLAS ═══════════ */
        .es-modal-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(10, 20, 30, 0.6);
            z-index: 1000;
            justify-content: center;
            align-items: center;
            padding: 24px;
            opacity: 0;
            transition: opacity 0.25s ease;
        }
        .es-modal-overlay.active { display: flex; }
        .es-modal-overlay.show { opacity: 1; }

        .es-modal-content {
            background: var(--snow);
            border-radius: 16px;
            max-width: 780px;
            width: 100%;
            max-height: 85vh;
            overflow-y: auto;
            padding: 36px;
            position: relative;
            transform: translateY(20px);
            transition: transform 0.25s ease;
        }
        .es-modal-overlay.show .es-modal-content { transform: translateY(0); }

        .es-modal-close {
            position: absolute;
            top: 14px;
            right: 18px;
            background: none;
            border: none;
            font-size: 28px;
            line-height: 1;
            color: var(--ink);
            cursor: pointer;
        }

        .es-rules-header { margin-bottom: 20px; }
        .es-rules-header .rq-eyebrow { margin-bottom: 8px; }
        .es-rules-header h2 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 500;
            color: var(--navy);
            margin: 0 0 8px;
        }
        .es-rules-header h2 em { font-style: italic; color: var(--accent); }
        .es-rules-header p { font-size: 13px; color: var(--muted); line-height: 1.6; margin: 0; }

        .es-rules-grid-modal {
            display: grid;
            grid-template-columns: 1fr;
            gap: 14px;
            margin-top: 20px;
        }
        @media (min-width: 620px) {
            .es-rules-grid-modal { grid-template-columns: 1fr 1fr; }
        }

        .es-rule-card {
            display: flex;
            gap: 12px;
            padding: 14px;
            border: 1.5px solid var(--border);
            border-radius: 10px;
            background: #fff;
        }
        .es-rule-card.es-rule-alert { border-color: rgba(213,48,37,0.3); background: #fdf4f3; }
        .es-rule-num {
            font-family: 'Playfair Display', serif;
            font-size: 15px;
            font-weight: 600;
            color: var(--accent);
            flex-shrink: 0;
        }
        .es-rule-title {
            font-size: 13px;
            font-weight: 700;
            color: var(--navy);
            margin: 0 0 4px;
        }
        .es-rule-text {
            font-size: 12.5px;
            color: var(--muted);
            line-height: 1.55;
            margin: 0;
        }

        .es-modal-footer {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: flex-end;
        }

        .es-link-reglas {
            background: none;
            border: none;
            color: var(--accent-deep);
            text-decoration: underline;
            cursor: pointer;
            font-size: inherit;
            font-family: inherit;
            padding: 0;
        }
    </style>
</head>
<body>

<div class="rq-shell">

    {{-- ═══ COLUMNA IZQUIERDA — TICKET ═══ --}}
    <aside class="rq-ticket-col">
        <div>
            <img src="{{ asset('images/zehcnas.png') }}" alt="Zehcnas Studio" class="rq-logo">
            <p class="rq-tagline">"El estudio es tuyo. Hazlo tuyo."</p>

            <button type="button" class="rq-mobile-toggle" id="rq-toggle">
                <span>Ver resumen de tu reserva</span>
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
            </button>

            <div class="rq-ticket-wrap" id="rq-ticket-wrap">
                <div class="rq-ticket">
                    <div class="rq-ticket-head">
                        <div>
                            <div class="rq-ticket-title">Solicitud de estudio</div>
                            <div class="rq-ticket-num" id="rq-ticket-num">Nº pendiente</div>
                        </div>
                        <span class="rq-ticket-stamp" id="rq-ticket-stamp">Borrador</span>
                    </div>

                    <div class="rq-ticket-perf"></div>

                    <div class="rq-ticket-rows">
                        <div class="rq-ticket-row">
                            <span class="rq-ticket-row-label">Fecha</span>
                            <span class="rq-ticket-row-value rq-empty" id="rq-t-fecha">Por definir</span>
                        </div>
                        <div class="rq-ticket-row">
                            <span class="rq-ticket-row-label">Horario</span>
                            <span class="rq-ticket-row-value rq-empty" id="rq-t-horario">Por definir</span>
                        </div>
                        <div class="rq-ticket-row">
                            <span class="rq-ticket-row-label">Finalidad</span>
                            <span class="rq-ticket-row-value rq-empty" id="rq-t-finalidad">Por definir</span>
                        </div>
                        <div class="rq-ticket-row">
                            <span class="rq-ticket-row-label">Personas</span>
                            <span class="rq-ticket-row-value rq-empty" id="rq-t-personas">Por definir</span>
                        </div>
                        <div class="rq-ticket-row">
                            <span class="rq-ticket-row-label">Contacto</span>
                            <span class="rq-ticket-row-value rq-empty" id="rq-t-contacto">Por definir</span>
                        </div>
                    </div>

                    <div class="rq-ticket-foot">Sujeto a confirmación por el equipo de Zehcnas Studio.</div>
                </div>
            </div>
            {{-- ══ MODAL REGLAS ══ --}}
            <div class="es-modal-overlay" id="modalReglas">
                <div class="es-modal-content">
                    <button type="button" class="es-modal-close" onclick="cerrarModalReglas()" aria-label="Cerrar">&times;</button>

                    <div class="es-rules-header">
                        <p class="rq-eyebrow">Antes de reservar</p>
                        <h2>Reglas y <em>términos</em></h2>
                        <p>Léelas antes de reservar. Están pensadas para garantizar la mejor experiencia para todos.</p>
                    </div>

                    <div class="es-rules-grid-modal">
                        <div class="es-rule-card"><span class="es-rule-num">01</span><div><h4 class="es-rule-title">Pago previo obligatorio</h4><p class="es-rule-text">El pago total de la renta debe realizarse para poder agendar la fecha y hora deseada.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">02</span><div><h4 class="es-rule-title">Puntualidad estricta</h4><p class="es-rule-text">Tienes 15 minutos previos a tu reserva para preparar equipos. La sesión debe concluir puntualmente. Tiempo adicional implica rentar horas extras.</p></div></div>
                        <div class="es-rule-card es-rule-alert"><span class="es-rule-num">03</span><div><h4 class="es-rule-title">Prohibido fumar</h4><p class="es-rule-text">Prohibido fumar cigarrillos y cigarrillos electrónicos dentro del estudio.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">04</span><div><h4 class="es-rule-title">Notifica tu grupo</h4><p class="es-rule-text">Informa la cantidad de personas. Para más de 15 personas se aplica un fee de limpieza de RD$500.</p></div></div>
                        <div class="es-rule-card es-rule-alert"><span class="es-rule-num">05</span><div><h4 class="es-rule-title">Cuida el ciclograma</h4><p class="es-rule-text">NO saltar, pisar, sentarse ni acostarse en la curva del ciclograma. La reposición tiene un valor de RD$50,000 hasta RD$250,000.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">06</span><div><h4 class="es-rule-title">Cuida los equipos</h4><p class="es-rule-text">Cuida los equipos, props, sets y espacios. En caso de daños, debes reponer el artículo con su valor total.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">07</span><div><h4 class="es-rule-title">Limpieza del espacio</h4><p class="es-rule-text">No dejar basura fuera del zafacón. Deja el espacio como lo encontraste.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">08</span><div><h4 class="es-rule-title">Responsabilidad con menores</h4><p class="es-rule-text">Cuando haya niños presentes, los padres o tutores son responsables por ellos y sus acciones dentro del estudio.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">09</span><div><h4 class="es-rule-title">Encargado siempre presente</h4><p class="es-rule-text">Siempre habrá un encargado de Zehcnas Studio durante toda la reserva para seguridad y asistencia con props, sets y luces.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">10</span><div><h4 class="es-rule-title">Objetos olvidados</h4><p class="es-rule-text">Objetos dejados en el estudio se guardan por 3 días. Pasado ese tiempo, serán desechados.</p></div></div>
                        <div class="es-rule-card"><span class="es-rule-num">11</span><div><h4 class="es-rule-title">Reprogramación</h4><p class="es-rule-text">Se permite reprogramar 1 sola vez sin costo adicional. A partir de la segunda reprogramación se cobra RD$500.</p></div></div>
                        <div class="es-rule-card es-rule-alert"><span class="es-rule-num">12</span><div><h4 class="es-rule-title">Incumplimiento</h4><p class="es-rule-text">El incumplimiento de cualquier regla da derecho a Zehcnas Studio a cancelar y expulsar de la renta de forma inmediata.</p></div></div>
                    </div>

                    <div class="es-modal-footer">
                        <button type="button" class="rq-btn" onclick="cerrarModalReglas()">Entendido</button>
                    </div>
                </div>
            </div>
        </div>

        <p class="rq-col-bottom-note">
            ¿Ya reservaste antes? Te contactaremos por WhatsApp para confirmar tu solicitud.<br>
            <a href="/estudio">← Volver a la página del estudio</a>
        </p>
    </aside>

    {{-- ═══ COLUMNA DERECHA — FORMULARIO ═══ --}}
    <main class="rq-form-col">
        <div class="rq-form-inner">

            <a href="/estudio" class="rq-back">
                <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="15 18 9 12 15 6"/></svg>
                Volver al estudio
            </a>

            <p class="rq-eyebrow">Reserva tu espacio</p>
            <h1 class="rq-heading">Cuéntanos qué <em>necesitas</em></h1>
            <p class="rq-subtext">
                Completa los datos y te confirmamos por WhatsApp. Revisa antes las
                <button type="button" class="es-link-reglas" onclick="abrirModalReglas()">reglas del estudio</button>.
            </p>

            {{-- Stepper --}}
            <div class="rq-stepper" id="rq-stepper">
                <div class="rq-step-node active" data-step-node="1">
                    <div class="rq-step-circle">1</div>
                    <span class="rq-step-label">Fecha</span>
                </div>
                <div class="rq-step-line" data-step-line="1"></div>
                <div class="rq-step-node" data-step-node="2">
                    <div class="rq-step-circle">2</div>
                    <span class="rq-step-label">Detalles</span>
                </div>
                <div class="rq-step-line" data-step-line="2"></div>
                <div class="rq-step-node" data-step-node="3">
                    <div class="rq-step-circle">3</div>
                    <span class="rq-step-label">Contacto</span>
                </div>
            </div>

            <div class="rq-general-error" id="rq-error-general"></div>

            {{-- PASO 1 --}}
            <div class="rq-step" data-step="1">
                <div class="rq-field">
                    <label for="rq-fecha">Fecha</label>
                    <input type="date" id="rq-fecha" name="fecha">
                    <p class="rq-error" data-error="fecha"></p>
                </div>

                <div class="rq-row-2">
                    <div class="rq-field">
                        <label for="rq-hora-inicio">Hora de inicio</label>
                        <select id="rq-hora-inicio" name="hora_inicio" disabled>
                            <option value="">Primero selecciona una fecha</option>
                        </select>
                    </div>
                    <div class="rq-field">
                        <label for="rq-hora-fin">Hora de fin</label>
                        <select id="rq-hora-fin" name="hora_fin" disabled>
                            <option value="">Primero elige la hora de inicio</option>
                        </select>
                    </div>
                </div>
                <p class="rq-error" data-error="hora_inicio"></p>

                <div class="rq-nav rq-nav-end">
                    <button type="button" class="rq-btn" data-next="1">Continuar</button>
                </div>
            </div>

            {{-- PASO 2 --}}
            <div class="rq-step" data-step="2" hidden>
                <div class="rq-field">
                    <label>Finalidad</label>
                    <div class="rq-radio-group" data-radio="finalidad">
                        <label class="rq-radio-option"><input type="radio" name="finalidad" value="fotografia"> Fotografía</label>
                        <label class="rq-radio-option"><input type="radio" name="finalidad" value="video"> Video</label>
                        <label class="rq-radio-option"><input type="radio" name="finalidad" value="podcast"> Podcast</label>
                        <label class="rq-radio-option"><input type="radio" name="finalidad" value="contenido_personal"> Contenido personal orgánico</label>
                        <label class="rq-radio-option"><input type="radio" name="finalidad" value="evento"> Evento</label>
                    </div>
                    <p class="rq-error" data-error="finalidad"></p>
                </div>

                <div class="rq-field">
                    <label for="rq-personas">¿Cuántas personas estarán presentes?</label>
                    <input type="number" id="rq-personas" name="cantidad_personas" min="1" max="50">
                    <p class="rq-error" data-error="cantidad_personas"></p>
                </div>

                <div class="rq-check-row">
                    <input type="checkbox" id="rq-fondo-toggle">
                    <label for="rq-fondo-toggle">¿Deseas un color de fondo adicional aparte de blanco?</label>
                </div>
                <div class="rq-field" id="rq-fondo-wrap" hidden>
                    <label for="rq-fondo-color">¿Qué color?</label>
                    <input type="text" id="rq-fondo-color" name="color_fondo" placeholder="Ej. gris, negro, azul">
                </div>

                <div class="rq-field">
                    <label>Iluminación a utilizar</label>
                    <div class="rq-radio-group" data-radio="iluminacion">
                        <label class="rq-radio-option"><input type="radio" name="iluminacion" value="luz_fija"> Luz fija</label>
                        <label class="rq-radio-option"><input type="radio" name="iluminacion" value="flashes"> Flashes</label>
                        <label class="rq-radio-option"><input type="radio" name="iluminacion" value="luz_natural"> Luz natural</label>
                        <label class="rq-radio-option"><input type="radio" name="iluminacion" value="luz_fija_flash"> Luz fija y flash</label>
                        <label class="rq-radio-option"><input type="radio" name="iluminacion" value="otro"> Otro</label>
                    </div>
                    <p class="rq-error" data-error="iluminacion"></p>
                </div>
                <div class="rq-field" id="rq-iluminacion-otro-wrap" hidden>
                    <label for="rq-iluminacion-otro">Especifica</label>
                    <input type="text" id="rq-iluminacion-otro" name="iluminacion_otro">
                </div>

                <div class="rq-nav">
                    <button type="button" class="rq-btn-ghost" data-back="2">← Atrás</button>
                    <button type="button" class="rq-btn" data-next="2">Continuar</button>
                </div>
            </div>

            {{-- PASO 3 --}}
            <div class="rq-step" data-step="3" hidden>
                <div class="rq-row-2">
                    <div class="rq-field">
                        <label for="rq-nombre">Nombre</label>
                        <input type="text" id="rq-nombre" name="nombre">
                        <p class="rq-error" data-error="nombre"></p>
                    </div>
                    <div class="rq-field">
                        <label for="rq-apellido">Apellido</label>
                        <input type="text" id="rq-apellido" name="apellido">
                        <p class="rq-error" data-error="apellido"></p>
                    </div>
                </div>

                <div class="rq-field">
                    <label for="rq-email">Correo electrónico</label>
                    <input type="email" id="rq-email" name="email">
                    <p class="rq-error" data-error="email"></p>
                </div>

                <div class="rq-field">
                    <label for="rq-telefono">Teléfono (WhatsApp)</label>
                    <input type="tel" id="rq-telefono" name="telefono" placeholder="809-000-0000">
                    <p class="rq-error" data-error="telefono"></p>
                </div>

                <div class="rq-field">
                    <label for="rq-invitados">¿Algo más que debamos saber? (opcional)</label>
                    <textarea id="rq-invitados" name="invitados" rows="3"></textarea>
                </div>

                <div class="rq-check-row">
                    <input type="checkbox" id="rq-terminos">
                    <label for="rq-terminos">He leído y acepto las <button type="button" class="es-link-reglas" onclick="abrirModalReglas()">
                            reglas del estudio
                        </button>.</label>
                </div>

                <p class="rq-error" data-error="terminos"></p>

                <div class="rq-nav">
                    <button type="button" class="rq-btn-ghost" data-back="3">← Atrás</button>
                    <button type="button" class="rq-btn" id="rq-enviar">
                        <span id="rq-enviar-texto">Enviar solicitud</span>
                    </button>
                </div>
            </div>

            {{-- PASO 4 — Confirmación --}}
            <div class="rq-step" data-step="4" hidden>
                <div class="rq-confirm">
                    <div class="rq-confirm-icon">
                        <svg width="30" height="30" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    </div>
                    <h2>Solicitud enviada</h2>
                    <p>
                        Recibimos tu solicitud para el <strong id="rq-fecha-confirmada"></strong>.
                        Te escribiremos por WhatsApp para confirmar la disponibilidad y coordinar el pago.
                    </p>
                    <p style="margin-top:20px;"><a href="/estudio" style="color:var(--accent-deep); font-weight:600;">Volver a la página del estudio</a></p>
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    (() => {
        const $  = (sel, ctx = document) => ctx.querySelector(sel);
        const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

        // ── Horario operativo del estudio ──
        const APERTURA_MIN = 8 * 60;       // 8:00am en minutos desde medianoche
        const CIERRE_MIN   = 22 * 60;      // 10:00pm
        const PASO_MIN     = 30;           // franjas de 30 minutos

        const form = {
            fecha: '', hora_inicio: '', hora_fin: '',
            finalidad: '', cantidad_personas: '', color_fondo_adicional: false, color_fondo: '',
            iluminacion: '', iluminacion_otro: '',
            nombre: '', apellido: '', email: '', telefono: '', invitados: '',
        };

        let ocupadosDelDia = [];

        // ── Toggle del ticket en mobile ──
        const toggleBtn = $('#rq-toggle');
        const ticketWrap = $('#rq-ticket-wrap');
        toggleBtn.addEventListener('click', () => {
            toggleBtn.classList.toggle('rq-open');
            ticketWrap.classList.toggle('rq-open');
        });

        const finalidadLabels = {
            fotografia: 'Fotografía', video: 'Video', podcast: 'Podcast',
            contenido_personal: 'Contenido personal', evento: 'Evento',
        };

        function actualizarTicket() {
            $('#rq-t-fecha').textContent = form.fecha
                ? new Date(form.fecha + 'T00:00:00').toLocaleDateString('es-DO', { day: 'numeric', month: 'short', year: 'numeric' })
                : 'Por definir';
            $('#rq-t-fecha').classList.toggle('rq-empty', !form.fecha);

            $('#rq-t-horario').textContent = (form.hora_inicio && form.hora_fin)
                ? `${form.hora_inicio} – ${form.hora_fin}`
                : 'Por definir';
            $('#rq-t-horario').classList.toggle('rq-empty', !(form.hora_inicio && form.hora_fin));

            $('#rq-t-finalidad').textContent = form.finalidad ? finalidadLabels[form.finalidad] : 'Por definir';
            $('#rq-t-finalidad').classList.toggle('rq-empty', !form.finalidad);

            $('#rq-t-personas').textContent = form.cantidad_personas ? `${form.cantidad_personas} persona(s)` : 'Por definir';
            $('#rq-t-personas').classList.toggle('rq-empty', !form.cantidad_personas);

            const contacto = form.nombre ? `${form.nombre} ${form.apellido}`.trim() : '';
            $('#rq-t-contacto').textContent = contacto || 'Por definir';
            $('#rq-t-contacto').classList.toggle('rq-empty', !contacto);
        }

        function limpiarError(campo) {
            const el = $(`[data-error="${campo}"]`);
            if (el) { el.textContent = ''; el.classList.remove('rq-show'); }
            const input = document.getElementById(`rq-${campo.replace(/_/g, '-')}`);
            if (input) input.classList.remove('rq-invalid');
        }
        function mostrarError(campo, mensaje) {
            const el = $(`[data-error="${campo}"]`);
            if (el) { el.textContent = mensaje; el.classList.add('rq-show'); }
            const input = document.getElementById(`rq-${campo.replace(/_/g, '-')}`);
            if (input) input.classList.add('rq-invalid');
        }

        function mostrarPaso(n) {
            $$('.rq-step').forEach(s => s.hidden = Number(s.dataset.step) !== n);
            if (n <= 3) {
                $$('.rq-step-node').forEach(node => {
                    const num = Number(node.dataset.stepNode);
                    node.classList.toggle('active', num === n);
                    node.classList.toggle('done', num < n);
                });
                $$('.rq-step-line').forEach(line => {
                    line.classList.toggle('done', Number(line.dataset.stepLine) < n);
                });
            } else {
                $('#rq-stepper').style.display = 'none';
            }
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }

        // ── Helpers de horas ──
        function minutosAHora(min) {
            const h = Math.floor(min / 60);
            const m = min % 60;
            return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
        }
        function horaAMinutos(hora) {
            const [h, m] = hora.split(':').map(Number);
            return h * 60 + m;
        }
        function a12h(hora24) {
            const [hStr, mStr] = hora24.split(':');
            let h = parseInt(hStr, 10);
            const ampm = h >= 12 ? 'PM' : 'AM';
            h = h % 12 || 12;
            return `${h}:${mStr} ${ampm}`;
        }

        // Convierte los rangos ocupados (ISO) a minutos-del-día, para comparar fácil
        function rangosOcupadosEnMinutos() {
            return ocupadosDelDia.map(o => ({
                inicio: horaAMinutos(o.hora_inicio.slice(11, 16)),
                fin:    horaAMinutos(o.hora_fin.slice(11, 16)),
            }));
        }

        // Todas las franjas de inicio posibles (cada 30 min) que no caen dentro de un ocupado
        function generarFranjasInicio() {
            const ocupados = rangosOcupadosEnMinutos();
            const libres = [];
            for (let min = APERTURA_MIN; min < CIERRE_MIN; min += PASO_MIN) {
                const dentroDeOcupado = ocupados.some(o => min >= o.inicio && min < o.fin);
                if (!dentroDeOcupado) libres.push(min);
            }
            return libres;
        }

        // Franjas de fin válidas para una hora de inicio dada:
        // posteriores al inicio, hasta el cierre o el próximo ocupado, lo que llegue primero
        function generarFranjasFin(inicioMin) {
            const ocupados = rangosOcupadosEnMinutos();
            const proximoOcupado = ocupados
                .filter(o => o.inicio >= inicioMin)
                .sort((a, b) => a.inicio - b.inicio)[0];
            const limite = proximoOcupado ? proximoOcupado.inicio : CIERRE_MIN;

            const libres = [];
            for (let min = inicioMin + PASO_MIN; min <= limite; min += PASO_MIN) {
                libres.push(min);
            }
            return libres;
        }

        function poblarSelectInicio() {
            const select = $('#rq-hora-inicio');
            select.innerHTML = '';

            const franjas = generarFranjasInicio();
            if (franjas.length === 0) {
                select.appendChild(new Option('No hay horarios disponibles ese día', ''));
                select.disabled = true;
                poblarSelectFin(null);
                return;
            }

            select.disabled = false;
            select.appendChild(new Option('Selecciona una hora', ''));
            franjas.forEach(min => {
                const hora = minutosAHora(min);
                select.appendChild(new Option(a12h(hora), hora));
            });
        }

        function poblarSelectFin(inicioMin) {
            const select = $('#rq-hora-fin');
            select.innerHTML = '';

            if (inicioMin === null) {
                select.appendChild(new Option('Primero elige la hora de inicio', ''));
                select.disabled = true;
                return;
            }

            const franjas = generarFranjasFin(inicioMin);
            if (franjas.length === 0) {
                select.appendChild(new Option('Sin horas de fin disponibles', ''));
                select.disabled = true;
                return;
            }

            select.disabled = false;
            select.appendChild(new Option('Selecciona una hora', ''));
            franjas.forEach(min => {
                const hora = minutosAHora(min);
                select.appendChild(new Option(a12h(hora), hora));
            });
        }

        // ── Paso 1 ──
        $('#rq-fecha').addEventListener('change', async (e) => {
            form.fecha = e.target.value;
            form.hora_inicio = '';
            form.hora_fin = '';
            limpiarError('fecha');
            actualizarTicket();
            if (form.fecha) await cargarDisponibilidad(form.fecha);
        });

        $('#rq-hora-inicio').addEventListener('change', (e) => {
            form.hora_inicio = e.target.value;
            form.hora_fin = '';
            limpiarError('hora_inicio');
            actualizarTicket();
            poblarSelectFin(form.hora_inicio ? horaAMinutos(form.hora_inicio) : null);
        });

        $('#rq-hora-fin').addEventListener('change', (e) => {
            form.hora_fin = e.target.value;
            actualizarTicket();
        });

        async function cargarDisponibilidad(fecha) {
            try {
                const res = await fetch(`{{ route('estudio.disponibilidad') }}?fecha=${fecha}`, { headers: { Accept: 'application/json' } });
                ocupadosDelDia = res.ok ? await res.json() : [];
            } catch (e) {
                ocupadosDelDia = [];
            }

            poblarSelectInicio();
            poblarSelectFin(null);
        }

        // ── Paso 2 ──
        $$('.rq-radio-group[data-radio="finalidad"] input').forEach(r =>
            r.addEventListener('change', (e) => { form.finalidad = e.target.value; limpiarError('finalidad'); actualizarTicket(); }));
        $('#rq-personas').addEventListener('input', (e) => { form.cantidad_personas = e.target.value; limpiarError('cantidad_personas'); actualizarTicket(); });
        $('#rq-fondo-toggle').addEventListener('change', (e) => {
            form.color_fondo_adicional = e.target.checked;
            $('#rq-fondo-wrap').hidden = !e.target.checked;
        });
        $('#rq-fondo-color').addEventListener('input', (e) => { form.color_fondo = e.target.value; });
        $$('.rq-radio-group[data-radio="iluminacion"] input').forEach(r =>
            r.addEventListener('change', (e) => {
                form.iluminacion = e.target.value;
                limpiarError('iluminacion');
                $('#rq-iluminacion-otro-wrap').hidden = e.target.value !== 'otro';
            }));
        $('#rq-iluminacion-otro').addEventListener('input', (e) => { form.iluminacion_otro = e.target.value; });

        // ── Paso 3 ──
        ['nombre', 'apellido', 'email', 'telefono'].forEach(campo => {
            document.getElementById(`rq-${campo}`).addEventListener('input', (e) => {
                form[campo] = e.target.value;
                limpiarError(campo);
                actualizarTicket();
            });
        });
        $('#rq-invitados').addEventListener('input', (e) => { form.invitados = e.target.value; });

        // ── Validación (red de seguridad; el select ya no debería ofrecer horarios ocupados) ──
        function seSolapaConOcupado(fecha, horaInicio, horaFin) {
            const nuevoInicio = horaAMinutos(horaInicio);
            const nuevoFin    = horaAMinutos(horaFin);
            return rangosOcupadosEnMinutos().some(o => nuevoInicio < o.fin && o.inicio < nuevoFin);
        }

        function validarPaso1() {
            let ok = true;
            if (!form.fecha) { mostrarError('fecha', 'Selecciona una fecha.'); ok = false; }
            if (!form.hora_inicio || !form.hora_fin) { mostrarError('hora_inicio', 'Indica hora de inicio y fin.'); ok = false; }
            else if (form.hora_fin <= form.hora_inicio) { mostrarError('hora_inicio', 'La hora de fin debe ser después de la de inicio.'); ok = false; }
            else if (seSolapaConOcupado(form.fecha, form.hora_inicio, form.hora_fin)) {
                mostrarError('hora_inicio', 'Ese horario se solapa con uno ya ocupado. Elige otro.');
                ok = false;
            }
            return ok;
        }
        function validarPaso2() {
            let ok = true;
            if (!form.finalidad) { mostrarError('finalidad', 'Selecciona una finalidad.'); ok = false; }
            if (!form.cantidad_personas || form.cantidad_personas < 1) { mostrarError('cantidad_personas', 'Indica cuántas personas asistirán.'); ok = false; }
            if (!form.iluminacion) { mostrarError('iluminacion', 'Selecciona una opción de iluminación.'); ok = false; }
            return ok;
        }
        function validarPaso3() {
            let ok = true;
            if (!form.nombre) { mostrarError('nombre', 'Requerido.'); ok = false; }
            if (!form.apellido) { mostrarError('apellido', 'Requerido.'); ok = false; }
            if (!form.email || !form.email.includes('@')) { mostrarError('email', 'Correo inválido.'); ok = false; }
            if (!form.telefono) { mostrarError('telefono', 'Requerido.'); ok = false; }
            if (!$('#rq-terminos').checked) { mostrarError('terminos', 'Debes aceptar las reglas del estudio.'); ok = false; }
            return ok;
        }

        $$('[data-next]').forEach(btn => btn.addEventListener('click', () => {
            const paso = Number(btn.dataset.next);
            if (paso === 1 && !validarPaso1()) return;
            if (paso === 2 && !validarPaso2()) return;
            mostrarPaso(paso + 1);
        }));
        $$('[data-back]').forEach(btn => btn.addEventListener('click', () => {
            mostrarPaso(Number(btn.dataset.back) - 1);
        }));

        // ── Envío ──
        $('#rq-enviar').addEventListener('click', async () => {
            if (!validarPaso3()) return;

            const boton = $('#rq-enviar');
            const texto = $('#rq-enviar-texto');
            const errorGeneral = $('#rq-error-general');
            errorGeneral.classList.remove('rq-show');
            boton.disabled = true;
            texto.textContent = 'Enviando…';

            try {
                const res = await fetch("{{ route('estudio.solicitud.store') }}", {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    },
                    body: JSON.stringify(form),
                });

                if (res.status === 422) {
                    const data = await res.json();
                    const errores = data.errors || {};
                    Object.keys(errores).forEach(campo => mostrarError(campo, errores[campo][0]));
                    errorGeneral.textContent = 'Revisa los datos marcados e intenta de nuevo.';
                    errorGeneral.classList.add('rq-show');
                    return;
                }
                if (!res.ok) {
                    errorGeneral.textContent = 'Algo salió mal. Intenta de nuevo en unos minutos.';
                    errorGeneral.classList.add('rq-show');
                    return;
                }

                $('#rq-fecha-confirmada').textContent = new Date(form.fecha + 'T00:00:00')
                    .toLocaleDateString('es-DO', { day: 'numeric', month: 'long', year: 'numeric' });
                $('#rq-ticket-stamp').textContent = 'Enviada';
                $('#rq-ticket-stamp').classList.add('rq-stamp-done');
                mostrarPaso(4);
            } catch (e) {
                errorGeneral.textContent = 'No pudimos enviar tu solicitud. Revisa tu conexión.';
                errorGeneral.classList.add('rq-show');
            } finally {
                boton.disabled = false;
                texto.textContent = 'Enviar solicitud';
            }
        });

        mostrarPaso(1);
    })();

    function abrirModalReglas() {
        const modal = document.getElementById('modalReglas');
        modal.classList.add('active');
        requestAnimationFrame(() => modal.classList.add('show'));
        document.body.style.overflow = 'hidden';
    }

    function cerrarModalReglas() {
        const modal = document.getElementById('modalReglas');
        modal.classList.remove('show');
        setTimeout(() => {
            modal.classList.remove('active');
            document.body.style.overflow = '';
        }, 250);
    }

    // Cerrar al hacer click fuera del contenido
    document.getElementById('modalReglas')?.addEventListener('click', (e) => {
        if (e.target.id === 'modalReglas') cerrarModalReglas();
    });

    // Cerrar con tecla ESC
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape') cerrarModalReglas();
    });
</script>
</body>
</html>
