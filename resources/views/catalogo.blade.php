@extends('layouts.landing')
@php
    $navLinks = [
        ['href' => '#galeria',   'label' => 'Catálogo'],
        ['href' => '#paquetes',  'label' => 'Paquetes'],
    ];
@endphp

@section('title', 'Estudio para Renta — Zehcnas Studio')

@section('content')

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

@endsection

@push('scripts')
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
@endpush

