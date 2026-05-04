<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio</title>
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
    <a href="#proceso"  onclick="closeMobileMenu()">Proceso</a>
    <a href="#contacto" onclick="closeMobileMenu()">Contacto</a>
    @auth
        <a href="{{ $dashboardRoute }}" onclick="closeMobileMenu()">Mi cuenta</a>
    @else
        <a href="{{ route('login') }}"    onclick="closeMobileMenu()">Iniciar sesión</a>
        <a href="{{ route('register') }}" onclick="closeMobileMenu()">Registrarse</a>
    @endauth
</div>

{{-- 1 ── HERO ── --}}
<section class="hero" id="inicio">
    <div id="heroCarousel" class="carousel slide carousel-fade" data-bs-ride="carousel" data-bs-interval="5000">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="https://images.unsplash.com/photo-1460978812857-470ed1c77af0?q=80&w=1295&auto=format&fit=crop" alt="Sesión fotográfica 1">
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1520390138845-fd2d229dd553?w=1600" alt="Sesión fotográfica 2">
            </div>
            <div class="carousel-item">
                <img src="https://images.unsplash.com/photo-1531746020798-e6953c6e8e04?w=1600" alt="Sesión fotográfica 3">
            </div>
        </div>
    </div>
    <div class="hero-content">
        <p class="hero-eyebrow">Fotografía profesional</p>
        <h1 class="hero-title">Cada momento<br>merece ser <em>eterno</em></h1>
        <p class="hero-subtitle">Capturamos tu historia con luz, emoción y un estilo atemporal que perdura.</p>
        @auth
            <a href="{{ route('cliente.reservas.paso1') }}" class="btn-hero">Reserva tu sesión</a>
        @else
            <a href="{{ route('login') }}" class="btn-hero">Reserva tu sesión</a>
        @endauth
    </div>
</section>

{{-- 2 ── BIENVENIDA ── --}}
<section class="section-bienvenida">
    <div class="container">
        <div class="bienvenida-box reveal">
            <p class="bienvenida-eyebrow">Bienvenidos a AS Studio</p>
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

{{-- 3 ── GALERÍA ── --}}
<section class="section-galeria" id="galeria">
    <div class="container-fluid px-0">
        <div class="galeria-header reveal">
            <p class="section-eyebrow">Nuestra galería</p>
            <h2 class="section-heading">Momentos que cuentan <em>historias</em></h2>
            <p class="section-subtext">
                Una selección de sesiones, celebraciones y momentos especiales capturados con emoción, detalle y un estilo atemporal.
            </p>
        </div>

        <div class="galeria-swipe-viewport" id="galeriaViewport">
            <div class="galeria-swipe-track" id="galeriaTrack">
                <div class="galeria-swipe-slide">
                    <img src="https://images.unsplash.com/photo-1532712938310-34cb3982ef74?q=80&w=1170&auto=format&fit=crop" alt="Foto 1">
                </div>
                <div class="galeria-swipe-slide">
                    <img src="https://images.unsplash.com/photo-1720049366694-ee5a7db7be02?q=80&w=1170&auto=format&fit=crop" alt="Foto 2">
                </div>
                <div class="galeria-swipe-slide">
                    <img src="https://images.unsplash.com/photo-1611826585949-b0ccabd2c1a4?q=80&w=687&auto=format&fit=crop" alt="Foto 3">
                </div>
                <div class="galeria-swipe-slide">
                    <img src="https://images.unsplash.com/photo-1559734840-f9509ee5677f?q=80&w=687&auto=format&fit=crop" alt="Foto 4">
                </div>
            </div>
        </div>

        <div class="galeria-swipe-dots" id="galeriaDots">
            <span class="active"></span>
            <span></span>
            <span></span>
            <span></span>
        </div>

        <div class="galeria-cta reveal">
            <a class="btn-catalogo">Conocer el Catálogo</a>
        </div>
    </div>
</section>

{{-- 4 ── PROCESO ── --}}
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
        <p class="proceso-counter"><span id="procesoActual">1</span> / {{ 5 }}</p>
    </div>
</section>

{{-- 5 ── GRATITUD / TESTIMONIO ── --}}
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
            <p class="gratitud-author">Laura Méndez</p>
        </div>
    </div>
</section>

{{-- 6 ── CONTACTO ── --}}
<section class="section-contacto" id="contacto">
    <div class="container">
        <div class="row align-items-start">
            <div class="col-md-6 reveal">
                <p class="section-label">Escríbenos</p>
                <h2 class="section-title">Contáctanos</h2>
                <p class="section-subtitle">¿Tienes alguna pregunta? Con gusto te respondemos.</p>

                <form>
                    <label class="contacto-label">Nombre</label>
                    <input type="text" class="contacto-input" placeholder="Tu nombre completo">

                    <label class="contacto-label">Correo</label>
                    <input type="email" class="contacto-input" placeholder="tu@correo.com">

                    <label class="contacto-label">Mensaje</label>
                    <textarea class="contacto-input" rows="4" placeholder="¿En qué podemos ayudarte?"></textarea>

                    <button type="submit" class="btn-contacto">Enviar mensaje</button>
                </form>
            </div>

            <div class="col-md-6 reveal reveal-delay-2">
                <div class="contacto-info">
                    <div class="contacto-info-item">
                        <p>Email</p>
                        <h6>contacto@asstudio.com</h6>
                    </div>
                    <div class="contacto-info-item">
                        <p>Teléfono</p>
                        <h6>+1 (809) 000-0000</h6>
                    </div>
                    <div class="contacto-info-item">
                        <p>Ubicación</p>
                        <h6>Santo Domingo, República Dominicana</h6>
                    </div>
                    <div class="contacto-info-item">
                        <p>Horario</p>
                        <h6>Lunes a Viernes, 9am – 6pm</h6>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

{{--FALTA EL ABOUT ME --}}

{{-- FOOTER ── --}}
<footer class="footer">
    <p class="footer-brand">AS Studio</p>
    <p>© {{ date('Y') }} AS Studio. Todos los derechos reservados.</p>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // ── NAVBAR SCROLL ──
    const navbar = document.getElementById('navbar');
    window.addEventListener('scroll', () => {
        navbar.classList.toggle('scrolled', window.scrollY > 80);
    });

    // ── HAMBURGER MENU ──
    const hamburger   = document.getElementById('hamburger');
    const mobileMenu  = document.getElementById('mobileMenu');

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
    let procesoIndex        = 0;
    const procesoTrack      = document.getElementById('procesoTrack');
    const procesoSlides     = document.querySelectorAll('.proceso-slide');
    const procesoActualEl   = document.getElementById('procesoActual');

    function updateProcesoCarousel() {
        procesoTrack.style.transform = `translateX(-${procesoIndex * 100}%)`;
        procesoSlides.forEach((s, i) => s.classList.toggle('active', i === procesoIndex));
        if (procesoActualEl) procesoActualEl.textContent = procesoIndex + 1;
    }

    function moveProcesoSlide(dir) {
        procesoIndex = (procesoIndex + dir + procesoSlides.length) % procesoSlides.length;
        updateProcesoCarousel();
    }

    // Auto-advance proceso
    setInterval(() => moveProcesoSlide(1), 4500);

    // ── GALERÍA SWIPE ──
    (function () {
        const viewport = document.getElementById('galeriaViewport');
        const track    = document.getElementById('galeriaTrack');
        const slides   = document.querySelectorAll('.galeria-swipe-slide');
        const dots     = document.querySelectorAll('#galeriaDots span');
        let current = 0, startX = 0, isDragging = false, dragDelta = 0;

        function slideWidth() { return slides[0].offsetWidth + 20; }

        function getOffset(index) {
            const vw     = viewport.offsetWidth;
            const sw     = slides[0].offsetWidth;
            const center = (vw - sw) / 2;
            return -(index * slideWidth()) + center;
        }

        function goTo(index, animate = true) {
            current = Math.max(0, Math.min(index, slides.length - 1));
            track.classList.toggle('no-transition', !animate);
            track.style.transform = `translateX(${getOffset(current)}px)`;
            slides.forEach((s, i) => s.classList.toggle('active', i === current));
            dots.forEach((d, i) => d.classList.toggle('active', i === current));
        }

        // Touch
        viewport.addEventListener('touchstart', e => { startX = e.touches[0].clientX; }, { passive: true });
        viewport.addEventListener('touchend',   e => {
            const diff = startX - e.changedTouches[0].clientX;
            if (Math.abs(diff) > 40) goTo(current + (diff > 0 ? 1 : -1));
        }, { passive: true });

        // Mouse drag
        viewport.addEventListener('mousedown', e => {
            isDragging = true; startX = e.clientX; dragDelta = 0;
            viewport.classList.add('dragging');
            track.classList.add('no-transition');
        });
        window.addEventListener('mousemove', e => {
            if (!isDragging) return;
            dragDelta = e.clientX - startX;
            track.style.transform = `translateX(${getOffset(current) + dragDelta}px)`;
        });
        window.addEventListener('mouseup', () => {
            if (!isDragging) return;
            isDragging = false;
            viewport.classList.remove('dragging');
            track.classList.remove('no-transition');
            if (Math.abs(dragDelta) > 60) goTo(current + (dragDelta < 0 ? 1 : -1));
            else goTo(current);
        });

        dots.forEach((d, i) => d.addEventListener('click', () => goTo(i)));

        goTo(0, false);
        window.addEventListener('resize', () => goTo(current, false));
    })();
</script>
</body>
</html>
