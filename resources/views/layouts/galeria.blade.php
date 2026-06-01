<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Galería') — Estudio</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">

    {{-- Estilos del layout base --}}
    <link rel="stylesheet" href="{{ asset('css/galeria.css') }}">
    @stack('styles')
</head>
<body>

{{-- ── TOPBAR ── --}}
<header class="gal-topbar">
    <div class="gal-topbar__left">
        <a href="{{ route('cliente.galeria') }}" class="gal-topbar__logo">
            <img src="{{ asset('images/logo.png') }}" alt="AS Studio" height="45">
        </a>
        <div class="gal-topbar__divider"></div>
    </div>
    <div style="text-align: center;">
        <span class="gal-topbar__title">@yield('topbar_title')</span>
        <span class="gal-topbar__subtitle">@yield('topbar_subtitle')</span>
    </div>
    <div class="gal-topbar__right">
        @yield('topbar_action')
    </div>
</header>

{{-- ── PESTAÑAS (opcional) ── --}}
@hasSection('tabs')
    <nav class="gal-tabnav">
        <div class="gal-tabnav__inner">
            @yield('tabs')
        </div>
    </nav>
@endif

{{-- ── CONTENIDO PRINCIPAL ── --}}
<main class="gal-body">
    @yield('content')
</main>

@stack('scripts')
</body>
</html>
