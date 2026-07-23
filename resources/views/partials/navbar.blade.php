{{-- ── NAVBAR ── --}}
<nav class="navbar-landing" id="navbar">
    <a href="/" class="navbar-logo">
        <img src="{{ asset('images/' . ($logoOverride ?? 'logo.png')) }}"
             alt="AStudio"
             height="40"
             @if(!empty($logoOverride)) style="filter: brightness(0);" @endif>
    </a>

    @php
        $navLinks ??= [
            ['href' => '#fotografo', 'label' => 'Nosotros'],
            ['href' => '#galeria',   'label' => 'Galería'],
            ['href' => '#proceso',   'label' => 'Proceso'],
            ['href' => '#estudio',   'label' => 'Estudio'],
            ['href' => '#contacto',  'label' => 'Contacto'],
        ];
    @endphp

    <ul class="navbar-links">
        @foreach($navLinks as $link)
            <li><a href="{{ $link['href'] }}">{{ $link['label'] }}</a></li>
        @endforeach
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

    <div class="navbar-hamburger-wrap">
        <button class="navbar-hamburger" id="hamburger" aria-label="Menú">
            <span></span><span></span><span></span>
        </button>
        @auth
            <span class="navbar-hamburger__badge" id="hamburgerBadge" style="display:none;" aria-hidden="true"></span>
        @endauth
    </div>
</nav>

{{-- Mobile drawer --}}
<div class="navbar-mobile-menu" id="mobileMenu">
    @auth
        <a href="#" class="mobile-notif-link" id="mobileNotifLink">
            <span>Notificaciones</span>
            <span class="mobile-notif-badge" id="mobileNotifBadge" style="display:none;" aria-hidden="true"></span>
        </a>
    @endauth
    @foreach($navLinks as $link)
        <a href="{{ $link['href'] }}" onclick="closeMobileMenu()">{{ $link['label'] }}</a>
    @endforeach
    @auth
        @if(!empty($showMiCuenta))
            <a href="{{ $dashboardRoute }}" onclick="closeMobileMenu()">Mi cuenta</a>
        @endif
    @else
        <a href="{{ route('login') }}"    onclick="closeMobileMenu()">Iniciar sesión</a>
        <a href="{{ route('register') }}" onclick="closeMobileMenu()">Registrarse</a>
    @endauth
</div>
