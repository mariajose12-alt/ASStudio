@extends('layouts.landing')

@section('title', 'AStudio')

@section('content')

    {{-- 1 ── HERO ── --}}
    <section class="hero" id="inicio">
        <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
            <div class="carousel-inner">
                <div class="carousel-item active">
                    <img src="{{ asset('images/medium/hero.webp') }}" alt="Sesión fotográfica 1">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/medium/hero1.webp') }}" alt="Sesión fotográfica 2">
                </div>
                <div class="carousel-item">
                    <img src="{{ asset('images/medium/maybehero.webp') }}" alt="Sesión fotográfica 3">
                </div>
            </div>
        </div>
        <div class="hero-content">
            <p class="hero-eyebrow">Fotografía profesional</p>
            <h1 class="hero-title">Cada momento<br>merece ser <em>eterno</em></h1>
            <p class="hero-subtitle">Capturamos tu historia con luz, emoción y un estilo atemporal que perdura.</p>
            @auth
                <a href="{{ route('cliente.reservas.paso1') }}" class="btn-hero">
                    Reserva tu sesión
                </a>
            @else
                <a href="{{ route('login', ['redirect' => '/cliente/reservas/paso1']) }}" class="btn-hero">
                    Reserva tu sesión
                </a>
            @endauth
        </div>
    </section>

    {{-- 2 ── BIENVENIDA ── --}}
    <section class="section-bienvenida">
        <div class="container">
            <div class="bienvenida-box reveal">
                <p class="bienvenida-eyebrow">Bienvenidos a AStudio</p>
                <h2 class="bienvenida-title">
                    Capturamos momentos que merecen quedarse para siempre
                </h2>
                <p class="bienvenida-text">
                    Cada sesión está pensada para resaltar tu esencia con una experiencia cercana, estética y cuidadosamente dirigida.
                    Creamos fotografías con emoción, detalle y un estilo atemporal que cuenta tu historia de forma auténtica.
                </p>
                <div class="section-divider"><div class="section-divider-dot"></div></div>
            </div>
        </div>
    </section>

    {{-- 3 ── ABOUT / FOTÓGRAFO ── --}}
    <section class="section-fotografo" id="fotografo">
        <div class="container">
            <div class="fotografo-inner reveal">

                <div class="fotografo-img-wrap">
                    <div class="fotografo-img-frame">
                        <img
                            src="{{ asset('images/perfil.png') }}"
                            alt="Fotógrafo de AS Studio"
                            class="fotografo-img"
                        >
                    </div>
                    <div class="fotografo-badge">
                        <span class="fotografo-badge-num">5+</span>
                        <span class="fotografo-badge-txt">años de<br>experiencia</span>
                    </div>
                </div>

                <div class="fotografo-content">
                    <p class="section-eyebrow fotografo-eyebrow">Quién está detrás del lente</p>
                    <h2 class="fotografo-titulo">
                        Hola, soy <em>Abraham Sánchez</em>
                    </h2>
                    <p class="fotografo-bio">
                        Hoy, después de más de cinco años dedicado a sesiones personales, bodas, quinces y
                        eventos corporativos, mi misión sigue siendo la misma: que cuando veas tus fotos,
                        sientas exactamente lo que sentiste ese día.
                    </p>

                    <div class="fotografo-stats">
                        <div class="foto-stat">
                            <span class="foto-stat-num">200+</span>
                            <span class="foto-stat-lbl">Sesiones realizadas</span>
                        </div>
                        <div class="foto-stat">
                            <span class="foto-stat-num">100+</span>
                            <span class="foto-stat-lbl">Bodas capturadas</span>
                        </div>
                        <div class="foto-stat">
                            <span class="foto-stat-num">100%</span>
                            <span class="foto-stat-lbl">Clientes satisfechos</span>
                        </div>
                    </div>

                    <div class="fotografo-firma">
                        <span class="firma-rol">Fotógrafo & fundador de AStudio</span>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- 4 ── GALERÍA ── --}}
    <section class="section-galeria" id="galeria">
        <div class="container-fluid px-0">
            <div class="galeria-header reveal">
                <p class="section-eyebrow">Nuestra galería</p>
                <h2 class="section-heading">Momentos que cuentan <em>historias</em></h2>
                <p class="section-subtext">
                    Una selección de sesiones, celebraciones y momentos especiales capturados con emoción, detalle y un estilo atemporal.
                </p>
            </div>

            <div class="galeria-carousel-premium">
                <div class="galeria-viewport-premium">
                    <div class="galeria-track-premium" id="galeríaTrackPremium"></div>
                    <button class="galeria-nav-btn galeria-nav-prev" id="galeríaPrev" aria-label="Anterior">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"></polyline></svg>
                    </button>
                    <button class="galeria-nav-btn galeria-nav-next" id="galeríaNext" aria-label="Siguiente">
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"></polyline></svg>
                    </button>
                </div>
            </div>

            <div class="galeria-footer-premium reveal">
                <a href="{{ route('catalogo') }}" class="btn-catalogo-premium">
                    Ver catálogo
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"></line><polyline points="12 5 19 12 12 19"></polyline></svg>
                </a>
            </div>
        </div>
    </section>

    {{-- 5 ── PROCESO ── --}}
    <section class="section-proceso" id="proceso">
        <div class="container">
            <div class="proceso-header reveal">
                <p class="section-eyebrow">Cómo trabajamos</p>
                <h2 class="section-heading">El proceso de tu<br><em>sesión fotográfica</em></h2>
            </div>

            <div class="proceso-carousel-wrapper">
                <button class="proceso-btn prev" onclick="moveProcesoSlide(-1)" aria-label="Anterior">&#10094;</button>
                <div class="proceso-carousel" id="procesoCarousel">
                    <div class="proceso-track" id="procesoTrack">
                        <div class="proceso-slide active">
                            <div class="paso-num">01</div>
                            <h4 class="paso-titulo">Completa tu Solicitud</h4>
                            <p class="paso-desc">Cuéntanos los detalles de tu sesión o evento para que podamos prepararlo todo.</p>
                        </div>
                        <div class="proceso-slide">
                            <div class="paso-num">02</div>
                            <h4 class="paso-titulo">Confirmamos tu reserva</h4>
                            <p class="paso-desc">El fotógrafo revisa tu solicitud y aprueba los detalles de tu reserva.</p>
                        </div>
                        <div class="proceso-slide">
                            <div class="paso-num">03</div>
                            <h4 class="paso-titulo">Capturamos tu evento</h4>
                            <p class="paso-desc">Llega el gran día y tomamos cada momento especial con atención al detalle.</p>
                        </div>
                        <div class="proceso-slide">
                            <div class="paso-num">04</div>
                            <h4 class="paso-titulo">Eliges tus favoritas</h4>
                            <p class="paso-desc">Revisas la selección y escoges las fotos que más te gustan.</p>
                        </div>
                        <div class="proceso-slide">
                            <div class="paso-num">05</div>
                            <h4 class="paso-titulo">Recibe tu galería final</h4>
                            <p class="paso-desc">Editamos tus fotos y las subimos a tu galería privada para descargarlas.</p>
                        </div>
                    </div>
                </div>
                <button class="proceso-btn next" onclick="moveProcesoSlide(1)" aria-label="Siguiente">&#10095;</button>
            </div>
            <p class="proceso-counter"><span id="procesoActual">1</span> / 5</p>
        </div>
    </section>

    {{-- 6 ── GRATITUD / TESTIMONIO ── --}}
    <section class="section-gratitud" id="gratitud">
        <div class="container">
            <div class="gratitud-box reveal">
                <p class="gratitud-eyebrow">Palabras de gratitud</p>
                <div class="gratitud-stars">
                    <div class="gratitud-star"></div>
                    <div class="gratitud-star"></div>
                    <div class="gratitud-star"></div>
                    <div class="gratitud-star"></div>
                    <div class="gratitud-star"></div>
                </div>
                <blockquote class="gratitud-quote">
                    No solo capturaron imágenes, nos regalaron recuerdos que vamos a atesorar toda la vida.
                </blockquote>
                <p class="gratitud-author"> </p>
            </div>
        </div>
    </section>

    {{-- 7 ── CONTACTO ── --}}
    <section class="section-contacto" id="contacto">
        <div class="container">
            <div class="row align-items-start">
                <div class="col-md-6 reveal">
                    <p class="section-label">Escríbenos</p>
                    <h2 class="section-title">Contáctanos</h2>
                    <p class="section-subtitle">¿Tienes alguna pregunta? Con gusto te respondemos.</p>

                    <form id="contactForm">
                        <input type="hidden" name="access_key" value="18437397-89e5-4dba-822b-f791616103de">
                        <input type="hidden" name="subject" value="Nuevo mensaje - AStudio">

                        <label class="contacto-label">Nombre</label>
                        <input type="text" name="name" class="contacto-input"
                               placeholder="Tu nombre completo" required>

                        <label class="contacto-label">Correo</label>
                        <input type="email" name="email" class="contacto-input"
                               placeholder="tu@correo.com" required>

                        <label class="contacto-label">Mensaje</label>
                        <textarea name="message" class="contacto-input" rows="4"
                                  placeholder="¿En qué podemos ayudarte?" required></textarea>

                        <button type="submit" class="btn-contacto" id="submitBtn">
                            Enviar mensaje
                        </button>
                        <p id="formMsg" style="display:none; margin-top:1rem; font-size:0.9rem;"></p>
                    </form>
                </div>

                <div class="col-md-6 reveal reveal-delay-2">
                    <div class="contacto-info">
                        <div class="contacto-info-item">
                            <p>Email</p>
                            <h6>astudiophotograpyy@gmail.com</h6>
                        </div>
                        <div class="contacto-info-item">
                            <p>Teléfono</p>
                            <h6>+1 (829) 642-6577</h6>
                        </div>
                        <div class="contacto-info-item">
                            <p>Ubicación</p>
                            <h6>Santiago, República Dominicana</h6>
                        </div>
                        <div class="contacto-info-item">
                            <p>Horario</p>
                            <h6>Lunes a Viernes, 11am – 10pm; Sábado, 8am - 10pm</h6>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

@endsection

@push('scripts')
    <script>
        // ── SCROLL REVEAL ──
        const revealEls = document.querySelectorAll('.reveal');
        const observer  = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                    observer.unobserve(entry.target);
                }
            });
        }, { threshold: 0.12, rootMargin: '0px 0px -40px 0px' });
        revealEls.forEach(el => observer.observe(el));

        // ── PROCESO CAROUSEL ──
        let procesoIndex      = 0;
        const procesoTrack    = document.getElementById('procesoTrack');
        const procesoSlides   = document.querySelectorAll('.proceso-slide');
        const procesoActualEl = document.getElementById('procesoActual');

        function updateProcesoCarousel() {
            procesoTrack.style.transform = `translateX(-${procesoIndex * 100}%)`;
            procesoSlides.forEach((s, i) => s.classList.toggle('active', i === procesoIndex));
            if (procesoActualEl) procesoActualEl.textContent = procesoIndex + 1;
        }

        function moveProcesoSlide(dir) {
            procesoIndex = (procesoIndex + dir + procesoSlides.length) % procesoSlides.length;
            updateProcesoCarousel();
        }

        setInterval(() => moveProcesoSlide(1), 4500);

        // ── Enviar Gmail ──
        document.getElementById('contactForm').addEventListener('submit', async function(e) {
            e.preventDefault();

            const btn = document.getElementById('submitBtn');
            const msg = document.getElementById('formMsg');

            btn.disabled = true;
            btn.textContent = 'Enviando...';

            const res = await fetch('https://api.web3forms.com/submit', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(Object.fromEntries(new FormData(this)))
            });

            const data = await res.json();

            if (data.success) {
                msg.style.color = 'green';
                msg.textContent = '¡Mensaje enviado! Te respondemos pronto.';
                this.reset();
            } else {
                msg.style.color = 'red';
                msg.textContent = 'Hubo un error. Intenta de nuevo.';
            }

            msg.style.display = 'block';
            btn.disabled = false;
            btn.textContent = 'Enviar mensaje';
        });

        // ── GALERÍA ──
        (function () {
            const images = [
                "{{ asset('images/medium/boda-galeria.webp') }}",
                "{{ asset('images/medium/cumpleanos-galeria.webp') }}",
                "{{ asset('images/medium/galeria.webp') }}",
                "{{ asset('images/medium/grad-galeria.webp') }}",
                "{{ asset('images/medium/maternidad-galeria.webp') }}",
                "{{ asset('images/medium/sport-galeria.webp') }}",
                "{{ asset('images/medium/studio-galeria.webp') }}",
                "{{ asset('images/medium/galeria2.webp') }}",
            ];

            const N = images.length;
            let currentIdx = 0;
            let animating  = false;

            const track   = document.getElementById('galeríaTrackPremium');
            const prevBtn = document.getElementById('galeríaPrev');
            const nextBtn = document.getElementById('galeríaNext');

            const cardPositions = ['peek-left', 'adj-left', 'center', 'adj-right', 'peek-right'];
            const cards = [];

            cardPositions.forEach((pos, idx) => {
                const card = document.createElement('div');
                card.className = `galeria-card-premium ${pos === 'center' ? 'center' : pos.includes('adj') ? 'adjacent ' + (pos.includes('left') ? 'left' : 'right') : 'peek ' + (pos.includes('left') ? 'left' : 'right')}`;
                const img = document.createElement('img');
                img.alt = 'Galería de fotografía';
                card.appendChild(img);
                track.appendChild(card);
                cards.push({ element: card, position: pos, imgIndex: idx });
            });

            function mod(n, m) { return ((n % m) + m) % m; }

            function render() {
                cards.forEach((card, slot) => {
                    const imgIdx = mod(currentIdx - 2 + slot, N);
                    card.element.querySelector('img').src = images[imgIdx];
                    card.element.className = 'galeria-card-premium';
                    if (slot === 2) {
                        card.element.classList.add('center');
                    } else if (slot === 1 || slot === 3) {
                        card.element.classList.add('adjacent', slot === 1 ? 'left' : 'right');
                    } else {
                        card.element.classList.add('peek', slot === 0 ? 'left' : 'right');
                    }
                });
            }

            function go(direction) {
                if (animating) return;
                animating  = true;
                currentIdx = mod(currentIdx + direction, N);
                render();
                setTimeout(() => { animating = false; }, 650);
            }

            prevBtn.addEventListener('click', () => go(-1));
            nextBtn.addEventListener('click', () => go(1));

            let startX = 0, dragging = false;
            track.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
            track.addEventListener('touchend',   e => {
                const diff = startX - e.changedTouches[0].clientX;
                if (Math.abs(diff) > 40) go(diff > 0 ? 1 : -1);
            }, { passive: true });

            track.addEventListener('mousedown', e => { dragging = true; startX = e.clientX; });
            window.addEventListener('mouseup',  () => { dragging = false; });
            track.addEventListener('mousemove', e => {
                if (!dragging) return;
                const diff = startX - e.clientX;
                if (Math.abs(diff) > 50) { go(diff > 0 ? 1 : -1); dragging = false; }
            });

            render();
            setInterval(() => go(1), 5500);
        })();
    </script>
@endpush
