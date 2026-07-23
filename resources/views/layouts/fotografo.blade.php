<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AStudio — @yield('title', 'Panel Fotógrafo')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.x.x/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    @stack('styles')
</head>
<body>

@php
    $navLinks = [
        ['href' => route('fotografo.dashboard'),       'label' => 'Dashboard'],
        ['href' => route('fotografo.calendario'),      'label' => 'Calendario'],
        ['href' => route('fotografo.reservas.index'),  'label' => 'Reservas'],
        ['href' => route('fotografo.sesiones.index'),  'label' => 'Sesiones'],
        ['href' => route('fotografo.nomina.index'),    'label' => 'Nomina'],
    ];
    $logoOverride = null;
@endphp
@include('partials.navbar')
@include('partials.notif-panel')

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="/" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="AStudio" height="45">
        </a>
        <div class="brand-sub">Panel Fotógrafo</div>
    </div>
    <div class="user-card" style="margin: 16px;">
        <div class="user-name">
            {{ Auth::user()->persona->nombre ?? '' }} {{ Auth::user()->persona->apellido ?? '' }}
        </div>
        <div class="user-email">{{ Auth::user()->email ?? '' }}</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <a href="{{ route('fotografo.dashboard') }}" class="nav-link {{ request()->routeIs('fotografo.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <a href="{{ route('fotografo.calendario') }}" class="nav-link {{ request()->routeIs('fotografo.calendario') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            Calendario
        </a>
        <a href="{{ route('fotografo.reservas.index') }}" class="nav-link {{ request()->routeIs('fotografo.reservas*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/>
            </svg>
            Reservas
        </a>
        <a href="{{ route('fotografo.sesiones.index') }}" class="nav-link {{ request()->routeIs('fotografo.sesiones.index') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 17a5 5 0 01-.916-9.916 5.002 5.002 0 019.832 0A5.002 5.002 0 0116 17m-7-5l3-3m0 0l3 3m-3-3v12"/>
            </svg>
            Sesiones
        </a>
        <a href="{{ route('fotografo.nomina.index') }}" class="nav-link {{ request()->routeIs('fotografo.nomina*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V6m0 10v2m0-12a9 9 0 110 18 9 9 0 010-18z"/>
            </svg>
            Mi Nómina
        </a>
    </nav>

    <div class="sidebar-footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn-logout">
                <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Cerrar sesión
            </button>
        </form>
    </div>
</aside>

<main class="main">
    <div class="topbar">
        <div style="display:flex; align-items:center; gap:14px;">
            <button class="sidebar-hamburger" onclick="toggleSidebar()">
                <svg width="22" height="22" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <line x1="3" y1="6" x2="21" y2="6" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="12" x2="21" y2="12" stroke-width="2" stroke-linecap="round"/>
                    <line x1="3" y1="18" x2="21" y2="18" stroke-width="2" stroke-linecap="round"/>
                </svg>
            </button>
            <div>
                <h1 class="page-title">@yield('title', 'Dashboard')</h1>
                @hasSection('subtitle')
                    <p style="color:var(--muted); font-size:14px; margin:8px 0 0 0;">@yield('subtitle')</p>
                @endif
            </div>
        </div>
        <div style="display:flex; align-items:center; gap:14px;">
            @yield('topbar-actions')
            @include('partials.notificaciones')
        </div>
    </div>
    <div class="content">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if($errors->any())
            <div class="alert alert-error">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        @yield('content')
    </div>
</main>

<style>
    .sidebar-hamburger {
        display: none;
        background: none;
        border: none;
        cursor: pointer;
        padding: 4px;
        color: var(--navy);
        flex-shrink: 0;
    }
    @media (max-width: 768px) {
        .sidebar-hamburger { display: flex; }
    }
    @media (min-width: 769px) {
        /* En desktop, ocultar el navbar del landing */
        .navbar-landing,
        .navbar-mobile-menu {
            display: none !important;
        }
    }

    /* ── Topbar: solo visible en desktop ── */
    .topbar {
        display: none;
    }

    @media (min-width: 769px) {
        .topbar {
            display: flex;
        }
    }

    /* Menú móvil del navbar — necesita display:flex para que la animación funcione */
    @media (max-width: 768px) {
        .navbar-hamburger {
            display: flex !important;
        }

        .main {
            padding-top: 80px; /* altura del navbar flotante (~64px) + un poco de aire */
        }

        .navbar-mobile-menu {
            display: flex; /* necesario; la clase .open controla opacity/pointer-events */
        }

        /* Ocultar los links de escritorio dentro del navbar en móvil */
        .navbar-links {
            display: none !important;
        }
    }
</style>

<script src="{{ asset('js/notificaciones.js') }}"></script>
<script>
    function toggleSidebar() {
        document.querySelector('.sidebar').classList.toggle('open');
        document.getElementById('sidebarOverlay').classList.toggle('open');
        document.body.style.overflow = document.querySelector('.sidebar').classList.contains('open') ? 'hidden' : '';
    }
    function closeSidebar() {
        document.querySelector('.sidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
    document.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', closeSidebar);
    });
</script>
@include('partials.navbar-scripts')
@stack('scripts')
</body>
</html>
