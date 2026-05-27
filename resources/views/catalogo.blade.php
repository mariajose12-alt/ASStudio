<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AStudio</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
</head>
<body>
{{-- ── NAVBAR ── --}}
<nav class="navbar-landing" id="navbar">
    <a href="/" class="navbar-logo">
        <img src="{{ asset('images/logo.png') }}" alt="AS Studio" height="40">
    </a>

    <ul class="navbar-links">
        <li><a href="#galeria">Galería</a></li>
        <li><a href="#paquetes">Paquetes</a></li>
        <li><a href="#fotografo">Nosotros</a></li>
        <li><a href="#contacto">Contacto</a></li>
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

    {{-- Hamburger mobile --}}
    <button class="navbar-hamburger" id="hamburger" aria-label="Menú">
        <span></span><span></span><span></span>
    </button>
</nav>

{{-- Mobile drawer --}}
<div class="navbar-mobile-menu" id="mobileMenu">
    <a href="#galeria"  onclick="closeMobileMenu()">Galería</a>
    <a href="#paquetes" onclick="closeMobileMenu()">Paquetes</a>
    <a href="#fotografo" onclick="closeMobileMenu()">Nosotros</a>
    <a href="#proceso"  onclick="closeMobileMenu()">Proceso</a>
    <a href="#contacto" onclick="closeMobileMenu()">Contacto</a>
    @auth
        <a href="{{ $dashboardRoute }}" onclick="closeMobileMenu()">Mi cuenta</a>
    @else
        <a href="{{ route('login') }}"    onclick="closeMobileMenu()">Iniciar sesión</a>
        <a href="{{ route('register') }}" onclick="closeMobileMenu()">Registrarse</a>
    @endauth
</div>

{{-- Catalogos --}}
<section class="cat2-section" id="galeria">
    <div class="cat2-bg-glow"></div>

    {{-- ── HEADER DE SECCIÓN ── --}}
    <div class="cat2-section-header reveal">
        <h2 class="cat2-section-title">
            Nuestro <em>Catálogo</em>
        </h2>
    </div>

    <div class="cat2-grid">

        {{-- ════ COL IZQUIERDA: SLIDER ════ --}}
        <div class="cat2-slider-col">

            {{-- Viewport: recorta el track, muestra 3 cards --}}
            <div class="cat2-viewport" id="cat2Viewport">

                {{-- Track: contiene TODAS las imágenes en fila --}}
                <div class="cat2-track" id="cat2Track">
                    {{-- Las cards se generan dinámicamente por JS --}}
                </div>

                <button class="cat2-arrow cat2-arrow--left"  id="cat2BtnPrev" aria-label="Anterior">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
                </button>
                <button class="cat2-arrow cat2-arrow--right" id="cat2BtnNext" aria-label="Siguiente">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
                </button>

            </div>

            {{-- Barra de progreso --}}
            <div class="cat2-progress-wrap">
                <div class="cat2-progress-bar" id="cat2ProgressBar"></div>
            </div>

        </div>

        {{-- ════ COL DERECHA: TEXTO ════ --}}
        <div class="cat2-info-col">
            <div class="cat2-info-inner" id="cat2InfoInner">
                <span class="cat2-number" id="cat2Number">01</span>
                <h2 class="cat2-title"   id="cat2Title">Artísticas</h2>
                <div class="cat2-divider"></div>
                <p  class="cat2-desc"    id="cat2Desc">
                    Sesiones con concepto visual profundo, iluminación dramática y una narrativa que transforma cada fotograma en obra de arte.
                </p>
            </div>
            <p class="cat2-total">/ 08</p>
        </div>

    </div>
</section>


<section class="pkg-section" id="paquetes">
    <div class="pkg-bg-glow"></div>

    {{-- Header --}}
    <div class="pkg-header reveal">
        <p class="pkg-eyebrow">Inversión en tus recuerdos</p>
        <h2 class="pkg-heading">Nuestros <em>Paquetes</em></h2>
        <p class="pkg-sub">Elige el que mejor se adapte a tu historia. Todos incluyen edición profesional.</p>
    </div>

    {{-- ════ PAQUETE STUDIO ════ --}}
    <div class="pkg-block reveal">

        <div class="pkg-block-header">
            <div class="pkg-block-icon">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><rect x="2" y="3" width="20" height="14" rx="2"/><circle cx="12" cy="10" r="3"/><path d="M8 21h8M12 17v4"/></svg>
            </div>
            <div>
                <h3 class="pkg-block-title">Paquete Studio</h3>
                <p class="pkg-block-sub">Fotografía de estudio de alta gama</p>
            </div>
        </div>

        {{-- Incluye --}}
        <div class="pkg-includes">
            <p class="pkg-includes-label">Incluye</p>
            <div class="pkg-tags">
                <span class="pkg-tag">Retoque de piel</span>
                <span class="pkg-tag">Limpieza de piel</span>
                <span class="pkg-tag">Colorización</span>
                <span class="pkg-tag">Elección de fondos</span>
                <span class="pkg-tag">Iluminación profesional</span>
            </div>
        </div>

        {{-- Cards de precios --}}
        <div class="pkg-cards">
            <div class="pkg-card reveal reveal-delay-1">
                <span class="pkg-card-qty">5</span>
                <span class="pkg-card-label">fotografías digitales</span>
                <span class="pkg-card-price">$5,500</span>
                <span class="pkg-card-currency">pesos</span>
            </div>
            <div class="pkg-card reveal reveal-delay-2">
                <span class="pkg-card-qty">10</span>
                <span class="pkg-card-label">fotografías digitales</span>
                <span class="pkg-card-price">$6,000</span>
                <span class="pkg-card-currency">pesos</span>
            </div>
            <div class="pkg-card reveal reveal-delay-3">
                <span class="pkg-card-qty">15</span>
                <span class="pkg-card-label">fotografías digitales</span>
                <span class="pkg-card-price">$7,500</span>
                <span class="pkg-card-currency">pesos</span>
            </div>
            <div class="pkg-card reveal reveal-delay-4">
                <span class="pkg-card-qty">20</span>
                <span class="pkg-card-label">fotografías digitales</span>
                <span class="pkg-card-price">$9,000</span>
                <span class="pkg-card-currency">pesos</span>
            </div>
        </div>

    </div>

    {{-- ════ PAQUETE VIP ════ --}}
    <div class="pkg-block pkg-block--vip reveal">

        <div class="pkg-vip-glow"></div>

        <div class="pkg-block-header">
            <div class="pkg-block-icon pkg-block-icon--vip">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
            </div>
            <div>
                <h3 class="pkg-block-title">Paquete <span class="pkg-vip-badge">✨ VIP ✨</span></h3>
                <p class="pkg-block-sub">La experiencia completa. Sin compromisos.</p>
            </div>
        </div>

        {{-- Incluye --}}
        <div class="pkg-includes">
            <p class="pkg-includes-label">Incluye</p>
            <div class="pkg-tags">
                <span class="pkg-tag pkg-tag--vip">Fotografía de alta gama</span>
                <span class="pkg-tag pkg-tag--vip">Retoque de piel profundo</span>
                <span class="pkg-tag pkg-tag--vip">Colorización editorial</span>
                <span class="pkg-tag pkg-tag--vip">Atención al detalle</span>
                <span class="pkg-tag pkg-tag--vip">Pre y post-producción</span>
                <span class="pkg-tag pkg-tag--vip">Maquillaje profesional</span>
                <span class="pkg-tag pkg-tag--vip">Asesoría en sesión</span>
                <span class="pkg-tag pkg-tag--vip">Refrigerios</span>
            </div>
        </div>

        {{-- Lista de beneficios --}}
        <ul class="pkg-vip-list">
            <li><span class="pkg-vip-dot"></span> Hasta <strong>45 fotografías digitales</strong></li>
            <li><span class="pkg-vip-dot"></span> <strong>5 fotografías 8×10</strong> impresas</li>
            <li><span class="pkg-vip-dot"></span> <strong>1 fotografía 16×20</strong> impresa y enmarcada</li>
            <li><span class="pkg-vip-dot"></span> <strong>3 horas</strong> de sesión de fotos</li>
            <li><span class="pkg-vip-dot"></span> Entrega de finales en <strong>48 horas</strong> tras la selección</li>
            <li><span class="pkg-vip-dot"></span> <strong>Álbum 10×13"</strong> con 10 pliegues</li>
        </ul>

        {{-- Precio VIP --}}
        <div class="pkg-vip-price-wrap">
            <span class="pkg-vip-price">$845</span>
            <span class="pkg-vip-currency">USD</span>
        </div>

    </div>

    {{-- ════ OPCIONAL ════ --}}
    <div class="pkg-optional reveal">
        <div class="pkg-optional-icon">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round"><path d="M20.59 13.41l-7.17 7.17a2 2 0 0 1-2.83 0L2 12V2h10l8.59 8.59a2 2 0 0 1 0 2.82z"/><line x1="7" y1="7" x2="7.01" y2="7"/></svg>
        </div>
        <div class="pkg-optional-body">
            <p class="pkg-optional-title">Impresión 2×3 </p>
            <p class="pkg-optional-desc">Fotografías en tamaño cartera con adhesivo resistente al agua. Perfectas como sticker.</p>
        </div>
        <div class="pkg-optional-price">
            <span>$100</span>
            <small>pesos c/u</small>
        </div>
    </div>

    {{-- Nota final --}}
    <p class="pkg-disclaimer reveal">* No se entregan fotografías sin editar.</p>

</section>

{{-- FOOTER ── --}}
<footer class="footer">
    <div class="footer-inner">

        {{-- Col 1: Logo + descripción + redes --}}
        <div class="footer-col footer-col-brand">
            <a href="/" class="footer-logo-link">
                <img src="{{ asset('images/logo.png') }}" alt="AS Studio" class="footer-logo-img">
            </a>
            <p class="footer-desc">
                Fotografía profesional con alma. Capturamos tus momentos más especiales con luz, emoción y un estilo atemporal que perdura.
            </p>
            <div class="footer-socials">
                <a href="https://www.instagram.com/abrahamsanchezgi?utm_source=ig_web_button_share_sheet&igsh=ZDNlZDc0MzIxNw==" target="_blank" rel="noopener" class="footer-social-link" aria-label="Instagram">
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

        {{-- Col 2: Navegación --}}
        <div class="footer-col">
            <h4 class="footer-col-title">Navegación</h4>
            <ul class="footer-links">
                <li><a href="#galeria">Galería</a></li>
                <li><a href="#paquetes">Paquetes</a></li>
                <li><a href="#proceso">Cómo trabajamos</a></li>
                <li><a href="#fotografo">Nosotros</a></li>
                <li><a href="#contacto">Contacto</a></li>
            </ul>
        </div>

        {{-- Col 3: Contacto / Ubicación --}}
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
                    <span>Lunes a Viernes, 11am – 10pm; Sábado, 8am - 10pm</span>
                </li>
            </ul>
        </div>

    </div>

    <div class="footer-bottom">
        <p>© {{ date('Y') }} AStudio. Todos los derechos reservados.</p>
        <p>Hecho con ♥ en Santiago, RD</p>
    </div>
</footer>

<script>
    const revealObserver = new IntersectionObserver((entries) => {
        entries.forEach(e => {
            if (e.isIntersecting) {
                e.target.classList.add('visible');
                revealObserver.unobserve(e.target);
            }
        });
    }, { threshold: 0.15 });

    document.querySelectorAll('.reveal').forEach(el => revealObserver.observe(el));

    (function () {
        /* ── Datos ── */
        const catalogs = [
            { img: "{{ asset('images/catalogos/Artisticas.png') }}", name: "Artísticas",  desc: "Sesiones con concepto visual profundo, iluminación dramática y una narrativa que transforma cada fotograma en obra de arte." },
            { img: "{{ asset('images/catalogos/Beauty.png') }}",     name: "Beauty",      desc: "Retratos que celebran la belleza auténtica: piel, textura, luz y detalles capturados con elegancia y sensibilidad." },
            { img: "{{ asset('images/catalogos/Cumpleanos.png') }}", name: "Cumpleaños",  desc: "Cada cumpleaños es único. Capturamos la alegría, los detalles y la esencia de ese día irrepetible." },
            { img: "{{ asset('images/catalogos/Editorial.png') }}",  name: "Editorial",   desc: "Fotografía de moda y editorial con dirección de arte, composición impecable y estética de revista." },
            { img: "{{ asset('images/catalogos/Graduacion.png') }}", name: "Graduación",  desc: "El inicio de una nueva etapa merece imágenes que transmitan orgullo, emoción y logro." },
            { img: "{{ asset('images/catalogos/Parejas.png') }}",    name: "Parejas",     desc: "Amor genuino en cada imagen. Sesiones cálidas y naturales que cuentan su historia juntos." },
            { img: "{{ asset('images/catalogos/Sport.png') }}",      name: "Sport",       desc: "Fuerza, movimiento y pasión. Fotografía deportiva que captura la energía en su momento cumbre." },
            { img: "{{ asset('images/catalogos/Studio.png') }}",     name: "Studio",      desc: "Sesiones en estudio con fondos e iluminación controlados para un resultado impecable y atemporal." }
        ];

        const N = catalogs.length;

        /* ── Estrategia: infinite clone trick ──────────────────────────────────
           Clonamos todo el array: [ ...catalogs, ...catalogs, ...catalogs ]
           El track tiene 3N items. Empezamos posicionados en el bloque central (índice N).
           Al llegar al borde, saltamos silenciosamente al bloque opuesto (sin transición).
        ──────────────────────────────────────────────────────────────────────── */
        const TOTAL_CARDS = N * 3;

        /* Cada card ocupa CARD_W % del viewport. Las laterales asoman PEEK_W %. */
        const CARD_W = 76;   /* % del viewport que ocupa la card central */
        const PEEK_W = 12;   /* % que asoman las cards laterales a cada lado */
        /* gap entre cards en % del viewport */
        const GAP_W  = (100 - CARD_W - PEEK_W * 2) / 2; /* ≈ 0 → ajustamos a fixed */

        const track      = document.getElementById('cat2Track');
        const viewport   = document.getElementById('cat2Viewport');
        const btnPrev    = document.getElementById('cat2BtnPrev');
        const btnNext    = document.getElementById('cat2BtnNext');
        const numberEl   = document.getElementById('cat2Number');
        const titleEl    = document.getElementById('cat2Title');
        const descEl     = document.getElementById('cat2Desc');
        const infoInner  = document.getElementById('cat2InfoInner');
        const progressBar= document.getElementById('cat2ProgressBar');

        /* ── Construir cards en el DOM ── */
        const allCards = [];
        for (let i = 0; i < TOTAL_CARDS; i++) {
            const cat = catalogs[i % N];
            const card = document.createElement('div');
            card.className = 'cat2-card';
            card.innerHTML = `<img src="${cat.img}" alt="${cat.name}" loading="lazy"><div class="cat2-card-dim"></div>`;
            track.appendChild(card);
            allCards.push(card);
        }

        /* ── Estado ── */
        let currentLogical = 0;   /* índice real 0–N-1 */
        let trackIndex     = N;   /* índice en el track clonado (empezamos en bloque central) */
        let animating      = false;

        /* ── Calcular translateX para un índice dado ──
           Queremos que card `idx` esté centrada en el viewport.
           Cada card ocupa CARD_W vw + un gap fijo.
           Usamos unidades absolutas desde JS calculadas en px.
        ── */
        function getOffset(idx) {
            /* Leemos el ancho real del viewport en px */
            const vw     = viewport.offsetWidth;
            const cardPx = vw * CARD_W / 100;
            const gapPx  = vw * 0.04;   /* 4vw de gap entre cards */
            const stepPx = cardPx + gapPx;
            /* Queremos que la card idx quede centrada: desplazamiento desde 0 */
            /* El centro del viewport = vw/2. El centro de la card idx = idx*stepPx + cardPx/2 */
            return -(idx * stepPx) + (vw / 2 - cardPx / 2);
        }

        /* ── Aplicar posición sin transición ── */
        function jumpTo(idx) {
            track.style.transition = 'none';
            track.style.transform  = `translateX(${getOffset(idx)}px)`;
        }

        /* ── Aplicar posición CON transición suave ── */
        function slideTo(idx, onDone) {
            track.style.transition = 'transform 0.52s cubic-bezier(0.77, 0, 0.175, 1)';
            track.style.transform  = `translateX(${getOffset(idx)}px)`;
            track.addEventListener('transitionend', function handler() {
                track.removeEventListener('transitionend', handler);
                if (onDone) onDone();
            });
        }

        /* ── Actualizar estilos de cards (dim/bright) ── */
        function updateCardStyles(centerIdx) {
            allCards.forEach((card, i) => {
                const dist = Math.abs(i - centerIdx);
                if (dist === 0) {
                    card.classList.add('cat2-card--active');
                    card.classList.remove('cat2-card--side');
                } else {
                    card.classList.remove('cat2-card--active');
                    card.classList.add('cat2-card--side');
                }
            });
        }

        /* ── Actualizar texto ── */
        function pad(n) { return String(n + 1).padStart(2, '0'); }

        function updateText(logicalIdx) {
            const cat = catalogs[logicalIdx];
            infoInner.classList.add('cat2-info--out');
            setTimeout(() => {
                numberEl.textContent = pad(logicalIdx);
                titleEl.textContent  = cat.name;
                descEl.textContent   = cat.desc;
                progressBar.style.width = ((logicalIdx + 1) / N * 100) + '%';
                infoInner.classList.remove('cat2-info--out');
            }, 220);
        }

        /* ── Ir a siguiente / anterior ── */
        function go(dir) {
            if (animating) return;
            animating = true;

            const nextTrackIdx   = trackIndex + dir;
            const nextLogical    = ((currentLogical + dir) % N + N) % N;

            slideTo(nextTrackIdx, () => {
                trackIndex   = nextTrackIdx;
                currentLogical = nextLogical;

                /* Si estamos en el primer o último bloque clonado → salto silencioso al central */
                if (trackIndex < 2 || trackIndex >= N * 2 + 2) {
                    trackIndex = trackIndex < 2
                        ? trackIndex + N
                        : trackIndex - N;
                    jumpTo(trackIndex);
                }

                updateCardStyles(trackIndex);
                animating = false;
            });

            updateText(nextLogical);
        }

        /* ── Eventos ── */
        btnPrev.addEventListener('click', () => go(-1));
        btnNext.addEventListener('click', () => go(1));

        /* Swipe táctil */
        let touchX = 0;
        viewport.addEventListener('touchstart', e => { touchX = e.changedTouches[0].clientX; }, { passive: true });
        viewport.addEventListener('touchend',   e => {
            const dx = e.changedTouches[0].clientX - touchX;
            if (Math.abs(dx) > 48) go(dx < 0 ? 1 : -1);
        });

        /* Recalcular offsets al rotar pantalla / resize */
        window.addEventListener('resize', () => { jumpTo(trackIndex); });

        /* ── Init ── */
        jumpTo(trackIndex);
        updateCardStyles(trackIndex);
        progressBar.style.width = (1 / N * 100) + '%';
    })();
</script>
