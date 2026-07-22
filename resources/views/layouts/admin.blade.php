<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>AStudio — @yield('title', 'Panel Admin')</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/glightbox/dist/css/glightbox.min.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.x.x/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @stack('styles')
</head>
<body>

@include('partials.notif-panel')

<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>
<aside class="sidebar">
    <div class="sidebar-logo">
        <a href="/" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="AStudio" height="45">
        </a>
        <div class="brand-sub">Panel Administrativo</div>
    </div>
    <div class="user-card" style="margin: 16px;">
        <div class="user-name">
            {{ Auth::user()->persona->nombre ?? '' }} {{ Auth::user()->persona->apellido ?? '' }}
        </div>
        <div class="user-email">{{ Auth::user()->email ?? '' }}</div>
    </div>

    <nav class="sidebar-nav">
        <div class="nav-section-label">Principal</div>
        <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
            Dashboard
        </a>
        <div class="nav-section-label">Gestión</div>
        <a href="{{ route('admin.empleados.index') }}" class="nav-link {{ request()->routeIs('admin.empleados*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Empleados
        </a>
        <a href="{{ route('admin.pagos.index') }}" class="nav-link {{ request()->routeIs('admin.pagos*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
            Comprobantes Pendientes
        </a>
        <a href="{{ route('admin.reservas.index') }}" class="nav-link {{ request()->routeIs('admin.reservas*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
            Reservas
        </a>
        <a href="{{ route('admin.paquetes.index') }}" class="nav-link {{ request()->routeIs('admin.paquetes*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            Paquetes Fotográficos
        </a>
        <a href="{{ route('admin.catalogos.index') }}" class="nav-link {{ request()->routeIs('admin.catalogos*') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            Catálogos
        </a>
        <a href="{{ route('admin.estudio') }}" class="nav-link {{ request()->routeIs('admin.estudio') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
            </svg>
            Estudio
        </a>
        <a href="{{ route('admin.nomina') }}" class="nav-link {{ request()->routeIs('admin.nomina') ? 'active' : '' }}">
            <svg fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
            </svg>
            Nomina
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
        @include('partials.notificaciones')
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
    /* Forzar colores FullCalendar globalmente */
    :root {
        --fc-button-bg-color: var(--white);
        --fc-button-border-color: var(--border);
        --fc-button-text-color: var(--muted);
        --fc-button-hover-bg-color: var(--blue-pale);
        --fc-button-hover-border-color: var(--blue-mid);
        --fc-button-active-bg-color: var(--blue-mid);
        --fc-button-active-border-color: var(--blue-mid);
        --fc-today-bg-color: rgba(232,119,34,0.05);
        --fc-event-bg-color: var(--blue-mid);
        --fc-event-border-color: var(--blue-mid);
        --fc-page-bg-color: var(--white);
        --fc-neutral-bg-color: var(--cloud);
        --fc-list-event-hover-bg-color: var(--blue-pale);
    }
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
</style>

<script src="https://cdn.jsdelivr.net/npm/glightbox/dist/js/glightbox.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/wheelzoom@4.0.1/wheelzoom.js"></script>
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
@stack('scripts')
</body>
</html>
