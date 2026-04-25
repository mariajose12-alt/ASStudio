<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>AS Studio — @yield('title', 'Mi cuenta')</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:ital,wght@0,400;0,600;0,700;1,400;1,600&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/landing.css') }}">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">

    <style>
        .navbar-app {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 200;
            padding: 0 3rem;
            height: 64px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            background: var(--ink);
            box-shadow: 0 4px 24px rgba(0,0,0,0.35);
        }

        .navbar-app .navbar-logo {
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.3rem;
            font-weight: 700;
            letter-spacing: 0.18em;
            text-transform: uppercase;
            color: #fff;
            text-decoration: none;
        }
        .navbar-app .navbar-logo span { color: #d8b48a; }

        .navbar-app .navbar-links {
            display: flex;
            align-items: center;
            gap: 2.5rem;
            list-style: none;
        }
        .navbar-app .navbar-links a {
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            font-size: 0.82rem;
            font-weight: 500;
            letter-spacing: 0.06em;
            transition: color 0.2s;
        }
        .navbar-app .navbar-links a:hover,
        .navbar-app .navbar-links a.active { color: #d8b48a; }

        .navbar-app-right {
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .navbar-app-user-link {
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .navbar-app-user-link:hover { opacity: 0.8; }

        .navbar-app-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            background: #d8b48a;
            color: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Cormorant Garamond', serif;
            font-size: 15px;
            font-weight: 700;
            flex-shrink: 0;
        }

        .navbar-app-username {
            font-size: 0.85rem;
            color: rgba(255,255,255,0.6);
            font-family: 'DM Sans', sans-serif;
        }

        .navbar-app-logout {
            background: none;
            border: 1.5px solid rgba(255,255,255,0.25);
            color: rgba(255,255,255,0.7);
            padding: 6px 16px;
            border-radius: 999px;
            font-family: 'DM Sans', sans-serif;
            font-size: 0.82rem;
            cursor: pointer;
            transition: all 0.2s;
        }
        .navbar-app-logout:hover {
            border-color: #d8b48a;
            color: #d8b48a;
        }

        body {
            display: block !important;
            background: #f4f2ee;
        }

        .app-page-header {
            margin-top: 64px;
            background: #fff;
            border-bottom: 1px solid #e8e4dc;
            padding: 1.5rem 0;
        }

        .app-page-header h1 {
            max-width: 760px;
            margin: 0 auto;
            padding: 0 2rem;
            font-family: 'Cormorant Garamond', serif;
            font-size: 1.7rem;
            font-weight: 700;
            color: #1a1a1a;
        }

        .app-main {
            max-width: 760px;
            margin: 2.5rem auto;
            padding: 0 2rem 4rem;
        }
    </style>

    @stack('styles')
</head>
<body>

@php
    $dashboardRoute = match(Auth::user()->getRol()) {
        'ADMINISTRADOR' => route('admin.dashboard'),
        'FOTOGRAFO'     => route('fotografo.dashboard'),
        'CLIENTE'       => route('cliente.dashboard'),
        default         => route('login'),
    };
@endphp

<nav class="navbar-app">
    <a href="/" class="navbar-logo">AS <span>Studio</span></a>

    <ul class="navbar-links">
        @if(Auth::user()->esAdministrador())
            <li>
                <a href="{{ route('admin.dashboard') }}" class="{{ request()->routeIs('admin.*') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
        @endif
        @if(Auth::user()->esCliente())
            <li>
                <a href="{{ route('cliente.dashboard') }}" class="{{ request()->routeIs('cliente.*') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
        @endif
        @if(Auth::user()->esFotografo())
            <li>
                <a href="{{ route('fotografo.dashboard') }}" class="{{ request()->routeIs('fotografo.*') ? 'active' : '' }}">
                    Dashboard
                </a>
            </li>
        @endif
    </ul>

    <div class="navbar-app-right">
        <a href="{{ $dashboardRoute }}" class="navbar-app-user-link">
            <div class="navbar-app-avatar">
                {{ strtoupper(substr(Auth::user()->persona->nombre ?? 'U', 0, 1)) }}
            </div>
            <span class="navbar-app-username">
                {{ Auth::user()->persona->nombre ?? Auth::user()->email }}
            </span>
        </a>

        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="navbar-app-logout">Salir</button>
        </form>
    </div>
</nav>

<div class="app-page-header">
    <h1>@yield('title', 'Mi cuenta')</h1>
</div>

<main class="app-main">
    @if(session('success'))
        <div class="alert alert-success" style="margin-bottom:1.5rem;">
            {{ session('success') }}
        </div>
    @endif

    @yield('content')
</main>

@stack('scripts')
</body>
</html>
