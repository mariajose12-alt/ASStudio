<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Términos y Condiciones — AS Studio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,600;1,400&family=Outfit:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <style>
        /* ── TÉRMINOS  ── */

        .tc-hero {
            background: linear-gradient(135deg, var(--ink) 0%, #3d1f00 100%);
            padding: 5rem 2rem 4.5rem;
            text-align: center;
            position: relative;
            overflow: hidden;
        }
        .tc-hero::before {
            content: '';
            position: absolute;
            inset: 0;
            background: radial-gradient(ellipse at 60% 40%, rgba(232,119,34,0.18) 0%, transparent 70%);
            pointer-events: none;
        }
        .tc-hero .section-eyebrow { position: relative; }
        .tc-hero h1 {
            font-family: var(--font-serif);
            font-size: clamp(2rem, 5vw, 3.25rem);
            color: #fff;
            font-weight: 600;
            line-height: 1.15;
            margin-bottom: 1rem;
            position: relative;
        }
        .tc-hero h1 em {
            font-style: italic;
            color: var(--orange);
            font-weight: 400;
        }
        .tc-hero-meta {
            color: rgba(255,255,255,0.45);
            font-size: 0.82rem;
            position: relative;
        }

        .navbar-landing .tc-back {
            color: #fff;
        }

        .navbar-landing.scrolled .tc-back {
            color: var(--muted);
        }

        /* ── LAYOUT ── */
        .tc-layout {
            max-width: 860px;
            margin: 0 auto;
            padding: 4rem 2rem 6rem;
        }

        /* ── INTRO ── */
        .tc-intro {
            text-align: center;
            margin-bottom: 3.5rem;
        }
        .tc-intro p {
            font-size: 1rem;
            line-height: 1.75;
            color: var(--muted);
            max-width: 640px;
            margin: 0 auto;
        }

        /* ── SECCIÓN ── */
        .tc-section {
            margin-bottom: 1.5rem;
            border-radius: 16px;
            border: 1px solid var(--border);
            background: #fff;
            overflow: hidden;
        }
        .tc-section-header {
            display: flex;
            align-items: center;
            gap: 1rem;
            padding: 1.25rem 1.75rem;
            background: var(--cloud);
            border-bottom: 1px solid var(--border);
        }
        .tc-section-icon {
            width: 40px;
            height: 40px;
            border-radius: 10px;
            background: var(--orange);
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            color: #fff;
        }
        .tc-section-icon svg {
            width: 18px;
            height: 18px;
            stroke: #fff;
        }
        .tc-section-title {
            font-family: var(--font-serif);
            font-size: 1.1rem;
            font-weight: 600;
            color: var(--ink);
            margin: 0;
            flex: 1;
        }
        .tc-num {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--orange);
            letter-spacing: 0.1em;
            background: rgba(232,119,34,0.12);
            padding: 0.2rem 0.55rem;
            border-radius: 20px;
        }
        .tc-section-body {
            padding: 1.5rem 1.75rem;
            display: flex;
            flex-direction: column;
            gap: 0.9rem;
        }

        /* ── REGLA ── */
        .tc-rule {
            display: flex;
            gap: 0.9rem;
            align-items: flex-start;
        }
        .tc-dot {
            width: 7px;
            height: 7px;
            border-radius: 50%;
            background: var(--orange);
            flex-shrink: 0;
            margin-top: 0.52rem;
        }
        .tc-rule p {
            font-size: 0.91rem;
            line-height: 1.7;
            color: #4a3020;
            margin: 0;
        }

        /* ── ALERTA ── */
        .tc-alert {
            background: #fff5f5;
            border: 1px solid #fecaca;
            border-radius: 10px;
            padding: 0.7rem 1rem;
        }
        .tc-alert .tc-dot { background: #dc2626; }
        .tc-alert p { color: #7f1d1d; }

        /* ── RESPONSIVE ── */
        @media (max-width: 600px) {
            .tc-hero { padding: 3.5rem 1.25rem 3rem; }
            .tc-layout { padding: 2rem 1.1rem 4rem; }
            .tc-section-header { padding: 1rem 1.1rem; }
            .tc-section-body { padding: 1rem 1.1rem; }
        }
    </style>
</head>
<body>

{{-- ── NAVBAR (mismo del landing) ── --}}
<nav class="navbar-landing" id="navbar">

    <a href="javascript:history.back()" class="navbar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="AS Studio" height="40" >
    </a>

    <ul class="navbar-links">
        <li><a href="/#galeria">Galería</a></li>
        <li><a href="/#paquetes">Paquetes</a></li>
        <li><a href="/#fotografo">Nosotros</a></li>
        <li><a href="/#contacto">Contacto</a></li>
    </ul>

    @auth
        @php
            $dashboardRoute = match(auth()->user()->getRol()) {
                'ADMINISTRADOR' => route('admin.dashboard'),
                'FOTOGRAFO'     => route('fotografo.dashboard'),
                'CLIENTE'       => route('cliente.dashboard'),
                default         => route('login'),
            };
        @endphp
        <a href="{{ $dashboardRoute }}" class="navbar-avatar">
            {{ strtoupper(substr(auth()->user()->persona->nombre ?? 'U', 0, 1)) }}
        </a>
    @else
        <div style="display:flex; align-items:center; gap:0.5rem;">
            <a href="{{ route('register') }}" class="btn-nav-signup">Sign up</a>
            <a href="{{ route('login') }}"    class="btn-nav-login">Log in</a>
        </div>
    @endauth

    <button class="navbar-hamburger" id="hamburger" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
</nav>

{{-- Mobile drawer --}}
<div class="navbar-mobile-menu" id="mobileMenu">
    <a href="/#galeria"   onclick="closeMobileMenu()">Galería</a>
    <a href="/#paquetes"  onclick="closeMobileMenu()">Paquetes</a>
    <a href="/#fotografo" onclick="closeMobileMenu()">Nosotros</a>
    <a href="/#contacto"  onclick="closeMobileMenu()">Contacto</a>
    @auth
        <a href="{{ $dashboardRoute }}" onclick="closeMobileMenu()">Mi cuenta</a>
    @else
        <a href="{{ route('login') }}"    onclick="closeMobileMenu()">Iniciar sesión</a>
        <a href="{{ route('register') }}" onclick="closeMobileMenu()">Registrarse</a>
    @endauth
</div>

{{-- ── HERO ── --}}
<header class="tc-hero">
    <p class="section-eyebrow">AS Studio</p>
    <h1>Términos y <em>Condiciones</em></h1>
    <p class="tc-hero-meta">Última actualización: {{ date('d \d\e F \d\e Y') }}</p>
</header>

{{-- ── CONTENIDO ── --}}
<main class="tc-layout">

    {{-- INTRO --}}
    <div class="tc-intro">
        <p>
            Al solicitar una sesión fotográfica con <strong>AS Studio</strong>, el cliente acepta las siguientes
            condiciones de servicio. Te pedimos leerlas con atención, ya que regulan la relación
            entre el estudio y el cliente durante todo el proceso, desde la reserva hasta la entrega final.
        </p>
        <div class="section-divider"><div class="section-divider-dot"></div></div>
    </div>

    {{-- 01 RESERVAS Y PAGOS --}}
    <div class="tc-section">
        <div class="tc-section-header">
            <div class="tc-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="4" width="18" height="18" rx="2"/>
                    <line x1="16" y1="2" x2="16" y2="6"/>
                    <line x1="8" y1="2" x2="8" y2="6"/>
                    <line x1="3" y1="10" x2="21" y2="10"/>
                </svg>
            </div>
            <h2 class="tc-section-title">Reservas y Pagos</h2>
            <span class="tc-num">01</span>
        </div>
        <div class="tc-section-body">
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Para confirmar una reserva es obligatorio realizar un <strong>abono del 50% del valor del paquete</strong> seleccionado. Sin este anticipo la sesión no quedará asegurada.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Las sesiones se pautan con <strong>fecha y hora exacta</strong>. El cliente debe estar presente a la hora acordada.</p>
            </div>
            <div class="tc-rule tc-alert">
                <div class="tc-dot"></div>
                <p><strong>Puntualidad:</strong> Pasados 15 minutos de la hora acordada sin aviso previo, la sesión se cancela automáticamente <strong>sin devolución del anticipo pagado</strong>.</p>
            </div>
        </div>
    </div>

    {{-- 02 DESARROLLO --}}
    <div class="tc-section">
        <div class="tc-section-header">
            <div class="tc-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <circle cx="12" cy="12" r="10"/>
                    <line x1="12" y1="8" x2="12" y2="12"/>
                    <line x1="12" y1="16" x2="12.01" y2="16"/>
                </svg>
            </div>
            <h2 class="tc-section-title">Desarrollo de la Sesión</h2>
            <span class="tc-num">02</span>
        </div>
        <div class="tc-section-body">
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Cada sesión cuenta con un <strong>tiempo determinado según el paquete contratado</strong>. Si se requiere tiempo adicional, se aplicarán costos extra según las tarifas vigentes.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Al finalizar la sesión se mostrará al cliente una <strong>revisión de las fotografías</strong> para confirmar que se cumplió el objetivo. Las correcciones solo se realizan en ese momento; ajustes posteriores generan costos extras.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>El contenido audiovisual será <strong>entregado en la fecha y hora acordadas</strong>. El cliente será notificado cuando su galería esté disponible.</p>
            </div>
            <div class="tc-rule tc-alert">
                <div class="tc-dot"></div>
                <p><strong>Prohibición de láseres:</strong> No se realizará ningún trabajo si hay láseres encendidos. Si no pueden apagarse, la sesión se cancela <strong>sin derecho a reembolso</strong>.</p>
            </div>
        </div>
    </div>

    {{-- 03 RESPONSABILIDAD --}}
    <div class="tc-section">
        <div class="tc-section-header">
            <div class="tc-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/>
                    <circle cx="12" cy="7" r="4"/>
                </svg>
            </div>
            <h2 class="tc-section-title">Responsabilidad del Cliente</h2>
            <span class="tc-num">03</span>
        </div>
        <div class="tc-section-body">
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>En caso de <strong>daños al espacio, fondos, props o materiales del estudio</strong> causados por el cliente o sus acompañantes, estos deberán cubrir los costos de reparación o reemplazo.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>El cliente es responsable de informar con anticipación cualquier <strong>necesidad especial o cambio en los detalles de la sesión</strong> para que el equipo pueda prepararse adecuadamente.</p>
            </div>
        </div>
    </div>

    {{-- 04 SELECCIÓN --}}
    <div class="tc-section">
        <div class="tc-section-header">
            <div class="tc-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <rect x="3" y="3" width="18" height="18" rx="2"/>
                    <circle cx="8.5" cy="8.5" r="1.5"/>
                    <polyline points="21 15 16 10 5 21"/>
                </svg>
            </div>
            <h2 class="tc-section-title">Selección de Fotografías</h2>
            <span class="tc-num">04</span>
        </div>
        <div class="tc-section-body">
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>El cliente dispone de <strong>24 horas para seleccionar sus fotografías favoritas</strong> desde la notificación de disponibilidad de la galería.</p>
            </div>
            <div class="tc-rule tc-alert">
                <div class="tc-dot"></div>
                <p><strong>Vencimiento del plazo:</strong> Sin selección en 24 horas, <strong>se pierde la prioridad en la cola de edición</strong> y deberá esperar la disponibilidad del fotógrafo.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Si el cliente <strong>excede la cantidad de fotografías</strong> incluida en su paquete, se generará automáticamente el costo adicional antes de proceder con la edición.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>No se permite <strong>combinar características de paquetes diferentes</strong> sin aplicar los costos extra definidos.</p>
            </div>
        </div>
    </div>

    {{-- 05 ENTREGA Y PAGO --}}
    <div class="tc-section">
        <div class="tc-section-header">
            <div class="tc-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="20 12 20 22 4 22 4 12"/>
                    <rect x="2" y="7" width="20" height="5"/>
                    <line x1="12" y1="22" x2="12" y2="7"/>
                    <path d="M12 7H7.5a2.5 2.5 0 0 1 0-5C11 2 12 7 12 7z"/>
                    <path d="M12 7h4.5a2.5 2.5 0 0 0 0-5C13 2 12 7 12 7z"/>
                </svg>
            </div>
            <h2 class="tc-section-title">Entrega y Pago Final</h2>
            <span class="tc-num">05</span>
        </div>
        <div class="tc-section-body">
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Las fotografías sin marca de agua y la <strong>opción de descarga se habilitan</strong> únicamente cuando el cliente haya completado el pago final en el plazo establecido.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>El sistema generará un <strong>comprobante de pago</strong> por cada transacción. Los registros quedan disponibles en el panel del cliente.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Los <strong>servicios adicionales</strong> se facturan de forma independiente y deben pagarse antes de ser procesados.</p>
            </div>
        </div>
    </div>

    {{-- 06 PRIVACIDAD --}}
    <div class="tc-section">
        <div class="tc-section-header">
            <div class="tc-section-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/>
                </svg>
            </div>
            <h2 class="tc-section-title">Privacidad y Uso de Imágenes</h2>
            <span class="tc-num">06</span>
        </div>
        <div class="tc-section-body">
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>AS Studio podrá usar fotografías de sesiones para <strong>fines promocionales</strong> en redes sociales y portafolio, salvo indicación explícita del cliente al momento de la reserva.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>Los <strong>datos personales del cliente</strong> se usan exclusivamente para gestión de reserva, pagos y comunicación relacionada al servicio.</p>
            </div>
            <div class="tc-rule">
                <div class="tc-dot"></div>
                <p>El cliente recibirá <strong>notificaciones automáticas</strong> sobre el estado de su reserva, pagos pendientes y disponibilidad de su galería.</p>
            </div>
        </div>
    </div>

</main>

{{-- ── FOOTER (mismo del landing) ── --}}
<footer class="footer">
    <div class="footer-inner">

        <div class="footer-col footer-col-brand">
            <a href="/" class="footer-logo-link">
                <img src="{{ asset('images/logo.png') }}" alt="AS Studio" class="footer-logo-img">
            </a>
            <p class="footer-desc">
                Fotografía profesional con alma. Capturamos tus momentos más especiales con luz, emoción y un estilo atemporal que perdura.
            </p>
            <div class="footer-socials">
                <a href="https://www.instagram.com/abrahamsanchezgi" target="_blank" rel="noopener" class="footer-social-link" aria-label="Instagram">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="2" width="20" height="20" rx="5" ry="5"/><circle cx="12" cy="12" r="4.5"/><circle cx="17.5" cy="6.5" r="1" fill="currentColor" stroke="none"/></svg>
                </a>
                <a href="https://tiktok.com/@asstudio" target="_blank" rel="noopener" class="footer-social-link" aria-label="TikTok">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M19.59 6.69a4.83 4.83 0 0 1-3.77-4.25V2h-3.45v13.67a2.89 2.89 0 0 1-2.88 2.5 2.89 2.89 0 0 1-2.89-2.89 2.89 2.89 0 0 1 2.89-2.89c.28 0 .54.04.79.1V9.01a6.32 6.32 0 0 0-.79-.05 6.34 6.34 0 0 0-6.34 6.34 6.34 6.34 0 0 0 6.34 6.34 6.34 6.34 0 0 0 6.33-6.34V8.69a8.18 8.18 0 0 0 4.78 1.52V6.76a4.85 4.85 0 0 1-1.01-.07z"/></svg>
                </a>
                <a href="https://wa.me/18090000000" target="_blank" rel="noopener" class="footer-social-link" aria-label="WhatsApp">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 0 1-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 0 1-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 0 1 2.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0 0 12.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 0 0 5.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 0 0-3.48-8.413z"/></svg>
                </a>
            </div>
        </div>

        <div class="footer-col">
            <h4 class="footer-col-title">Navegación</h4>
            <ul class="footer-links">
                <li><a href="/#galeria">Galería</a></li>
                <li><a href="/#paquetes">Paquetes</a></li>
                <li><a href="/#proceso">Cómo trabajamos</a></li>
                <li><a href="/#fotografo">Nosotros</a></li>
                <li><a href="/#contacto">Contacto</a></li>
            </ul>
        </div>

        <div class="footer-col">
            <h4 class="footer-col-title">Encuéntranos</h4>
            <ul class="footer-info">
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"/><circle cx="12" cy="10" r="3"/></svg>
                    <span>C. E. León Jiménez,<br>Santiago de los Caballeros 51000</span>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07A19.5 19.5 0 0 1 4.69 12 19.79 19.79 0 0 1 1.61 3.4 2 2 0 0 1 3.6 1.22h3a2 2 0 0 1 2 1.72c.127.96.361 1.903.7 2.81a2 2 0 0 1-.45 2.11L7.91 8.77a16 16 0 0 0 6 6l.91-.91a2 2 0 0 1 2.11-.45c.907.339 1.85.573 2.81.7A2 2 0 0 1 21.73 16z"/></svg>
                    <span>+1 (809) 000-0000</span>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    <span>contacto@asstudio.com</span>
                </li>
                <li>
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>
                    <span>Lunes – Viernes, 9am – 6pm</span>
                </li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} AS Studio. Todos los derechos reservados.</p>
        <p>Hecho con ♥ en Santiago, RD</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── NAVBAR SCROLL ──
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 80);
    });

    // ── HAMBURGER MENU ──
    const hamburger  = document.getElementById('hamburger');
    const mobileMenu = document.getElementById('mobileMenu');

    hamburger.addEventListener('click', () => {
        const isOpen = mobileMenu.classList.toggle('open');
        hamburger.classList.toggle('open', isOpen);
        document.body.style.overflow = isOpen ? 'hidden' : '';
    });

    function closeMobileMenu() {
        mobileMenu.classList.remove('open');
        hamburger.classList.remove('open');
        document.body.style.overflow = '';
    }
</script>
</body>
</html>
