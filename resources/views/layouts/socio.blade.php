<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title', 'Panel Socio') · Zehcnas Studio</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;500;600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/socio-layout.css') }}">
    @stack('styles')
</head>
<body class="layout-socio">

<header class="socio-header">
    <a href="{{ route('socio.estudio.dashboard') }}" class="socio-header__brand">
        <img src="{{ asset('images/zehcnas.png') }}" alt="Zehcnas Studio" style="filter: brightness(0) invert(1);">
        <span class="socio-header__wordmark">Zehcnas</span>
    </a>

    {{-- Nav de escritorio --}}
    <nav class="socio-header__nav" role="tablist">
        <button class="estudio-subnav__tab is-active" data-tab="resumen" role="tab">
            Resumen
        </button>

        <button class="estudio-subnav__tab" data-tab="calendario" role="tab">
            Calendario
        </button>

        <button class="estudio-subnav__tab" data-tab="propuestas" role="tab">
            Propuestas

            @if($pendientesCount > 0)
                <span class="estudio-subnav__count">
                {{ $pendientesCount }}
            </span>
            @endif
        </button>
    </nav>

    <div class="socio-header__actions">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="socio-header__logout">Cerrar sesión</button>
        </form>
    </div>

    {{-- Botón hamburguesa — solo móvil --}}
    <button type="button" class="socio-header__toggle" id="socio-menu-toggle" aria-label="Abrir menú" aria-expanded="false">
        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
            <line x1="4" y1="7" x2="20" y2="7"/><line x1="4" y1="12" x2="20" y2="12"/><line x1="4" y1="17" x2="20" y2="17"/>
        </svg>
    </button>
</header>

{{-- Menú móvil --}}
<div class="socio-menu-overlay" id="socio-menu-overlay"></div>
<div class="socio-menu" id="socio-menu">
    <ul class="socio-menu__list">
        <li><button class="estudio-subnav__tab" data-tab="resumen">Resumen</button></li>
        <li><button class="estudio-subnav__tab" data-tab="calendario">Calendario</button></li>
        <li>
            <button class="estudio-subnav__tab" data-tab="propuestas">
                Propuestas
                @if($pendientesCount > 0)
                    <span class="estudio-subnav__count">{{ $pendientesCount }}</span>
                @endif
            </button>
        </li>
    </ul>
    <div class="socio-menu__footer">
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="socio-menu__logout">Cerrar sesión</button>
        </form>
    </div>
</div>


<main class="socio-main">
    @yield('content')
</main>

@stack('scripts')

<script>
    (function() {
        const toggle  = document.getElementById('socio-menu-toggle');
        const menu    = document.getElementById('socio-menu');
        const overlay = document.getElementById('socio-menu-overlay');

        function cerrarMenu() {
            menu.classList.remove('is-open');
            overlay.classList.remove('is-open');
            toggle.setAttribute('aria-expanded', 'false');
        }

        toggle.addEventListener('click', () => {
            const abierto = menu.classList.toggle('is-open');
            overlay.classList.toggle('is-open', abierto);
            toggle.setAttribute('aria-expanded', abierto ? 'true' : 'false');
        });

        overlay.addEventListener('click', cerrarMenu);
        menu.querySelectorAll('a').forEach(a => a.addEventListener('click', cerrarMenu));
    })();
</script>

</body>
</html>
