<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AStudio — @yield('title', 'Mi Cuenta')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    {{-- Tabler Icons (outline) --}}
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.x.x/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/galeria.css') }}">
    {{-- Mobile-first overrides —  cargar DESPUÉS de styles.css --}}
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    @stack('styles')
</head>
<body>

@php
    $navLinks = [
        ['href' => route('cliente.dashboard'),     'label' => 'Inicio'],
        ['href' => route('cliente.galeria'),        'label' => 'Galería'],
        ['href' => route('cliente.reservas.index'), 'label' => 'Reservas'],
        ['href' => route('cliente.pagos.index'),    'label' => 'Pagos'],
        ['href' => route('cliente.perfil'),         'label' => 'Perfil'],
    ];
    $logoOverride = null;
@endphp
@include('partials.navbar')
@include('partials.notif-panel')

<div class="layout">

    {{-- ── SIDEBAR (solo desktop ≥769px) ────────────────────── --}}
    <aside class="sidebar" id="appSidebar" aria-label="Menú principal">

        <div class="sidebar-logo">
            <a href="/" class="navbar-logo" aria-label="Ir al inicio de AStudio">
                <img src="{{ asset('images/logo.png') }}" alt="AStudio" height="45">
            </a>
            <div class="brand-sub">Mi Cuenta</div>
        </div>

        <div class="user-card" style="margin: 10px 8px;">
            <div class="user-name">
                {{ Auth::user()->persona->nombre ?? '' }} {{ Auth::user()->persona->apellido ?? '' }}
            </div>
            <div class="user-role">{{ Auth::user()->email ?? '' }}</div>
        </div>

        <nav class="sidebar-nav" aria-label="Navegación principal">
            <div class="nav-section-label">Principal</div>
            <a href="{{ route('cliente.dashboard') }}"
               class="nav-link {{ request()->routeIs('cliente.dashboard') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('cliente.dashboard') ? 'page' : 'false' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                </svg>
                Inicio
            </a>
            <a href="{{ route('cliente.galeria') }}"
               class="nav-link {{ request()->routeIs('cliente.galeria') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('cliente.galeria') ? 'page' : 'false' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Galería
            </a>
            <a href="{{ route('cliente.reservas.index') }}"
               class="nav-link {{ request()->routeIs('cliente.reservas*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('cliente.reservas*') ? 'page' : 'false' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                </svg>
                Reservas
                @if(isset($reservasPendientes) && $reservasPendientes > 0)
                    <span class="nav-badge" aria-label="{{ $reservasPendientes }} reservas pendientes">
                        {{ $reservasPendientes }}
                    </span>
                @endif
            </a>
            <a href="{{ route('cliente.pagos.index') }}"
               class="nav-link {{ request()->routeIs('cliente.pagos*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('cliente.pagos*') ? 'page' : 'false' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                Pagos Pendientes
            </a>

            <div class="nav-section-label">Cuenta</div>
            <a href="{{ route('cliente.perfil') }}"
               class="nav-link {{ request()->routeIs('cliente.perfil*') ? 'active' : '' }}"
               aria-current="{{ request()->routeIs('cliente.perfil*') ? 'page' : 'false' }}">
                <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                </svg>
                Perfil
            </a>
        </nav>

        <div class="sidebar-footer">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="btn-logout">
                    <svg width="15" height="15" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                    </svg>
                    Cerrar sesión
                </button>
            </form>
        </div>
    </aside>

    {{-- ── MAIN ────────────────────────────────────────────────── --}}
    <main class="main" id="mainContent">

        {{-- TOPBAR --}}
        <header class="topbar" role="banner">

            {{-- Desktop: título de la página --}}
            <div class="topbar__center d-none d-md-block">
                <h1 class="topbar__title">@yield('title', 'Inicio')</h1>
            </div>

            {{-- Acciones siempre visibles --}}
            <div class="topbar__actions">
                {{-- Botón "Nueva reserva" visible solo en desktop (dentro del topbar) --}}
                <a href="{{ route('cliente.reservas.paso1') }}"
                   class="btn-nueva-reserva d-none d-md-inline-flex"
                   aria-label="Crear nueva reserva">
                    <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                    </svg>
                    Nueva reserva
                </a>

                {{-- Notificaciones --}}
                @include('partials.notificaciones')

                {{-- Avatar / enlace al perfil --}}
                <a href="{{ route('cliente.perfil') }}" class="topbar__avatar"
                   aria-label="Ver mi perfil">
                    {{ strtoupper(substr(Auth::user()->persona->nombre ?? 'C', 0, 1)) }}{{ strtoupper(substr(Auth::user()->persona->apellido ?? 'L', 0, 1)) }}
                </a>
            </div>
        </header>

        {{-- Subtítulo / sección adicional del topbar (desktop) --}}
        @hasSection('topbar-actions')
            <div class="topbar-extra d-none d-md-flex">
                @yield('topbar-actions')
            </div>
        @endif

        {{-- CONTENIDO --}}
        <div class="content">
            @if(session('success'))
                <div class="alert alert-success" role="alert">{{ session('success') }}</div>
            @endif
            @if($errors->any())
                <div class="alert alert-error" role="alert">
                    @foreach($errors->all() as $error)
                        <div>{{ $error }}</div>
                    @endforeach
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>

{{-- ═════════════════════════════════════════
     Utilitarios display (reemplaza d-none/d-md-*)
     ═════════════════════════════════════════ --}}
<style>
    @media (max-width: 768px) {
        .navbar-hamburger {
            display: flex !important;
        }

        .layout {
            padding-top: 80px;
        }

        .navbar-mobile-menu {
            display: flex;
        }

        .navbar-links {
            display: none !important;
        }
    }

    /* Badge en sidebar nav */
    .nav-badge {
        margin-left: auto;
        background: var(--error);
        color: #fff;
        font-size: 9px;
        font-weight: 500;
        padding: 2px 6px;
        border-radius: 10px;
        line-height: 1.4;
    }
    /* Helpers display para topbar */
    .d-none { display: none !important; }
    @media (min-width: 769px) {
        .d-md-block      { display: block !important; }
        .d-md-flex       { display: flex !important; }
        .d-md-inline-flex{ display: inline-flex !important; }
    }
    /* Extra acciones topbar desktop */
    .topbar-extra {
        align-items: center;
        justify-content: flex-end;
        padding: 8px 24px;
        gap: 10px;
        border-bottom: 0.5px solid var(--border);
        background: var(--white);
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
</style>

<script src="{{ asset('js/notificaciones.js') }}"></script>
<script>
    /* Drawer lateral (solo móvil si se usa en el futuro) */
    function toggleSidebar() {
        const sidebar = document.getElementById('appSidebar');
        const overlay = document.getElementById('sidebarOverlay');
        const open    = sidebar.classList.toggle('open');
        overlay.classList.toggle('open', open);
        document.body.style.overflow = open ? 'hidden' : '';
    }
    function closeSidebar() {
        document.getElementById('appSidebar').classList.remove('open');
        document.getElementById('sidebarOverlay').classList.remove('open');
        document.body.style.overflow = '';
    }
</script>

@include('partials.navbar-scripts')
@stack('scripts')
@if(isset($cuentas))
    <x-modal-cuentas :cuentas="$cuentas" />
@endif
</body>
</html>
