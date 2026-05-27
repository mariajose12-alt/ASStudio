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
