<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio — Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="layout-auth">
<div class="auth-card">
    <!-- Panel izquierdo -->
    <div class="panel-left">
        <div>
            <div style="filter: brightness(0) invert(1);">
                <a href="/" class="navbar-logo">
                    <img src="{{ asset('images/logo.png') }}" alt="AS Studio" height="45">
                </a>
            </div>
            <div class="brand-sub">Fotografía profesional</div>
        </div>
        <div>
            <div class="left-quote">
                Cada imagen cuenta tu <em>historia</em> más auténtica.
            </div>
            <div class="left-cite">Panel Administrativo</div>
        </div>
        <div class="left-bottom">© {{ date('Y') }} AS Studio</div>
    </div>

    <!-- Formulario -->
    <div class="panel-right">
        <div class="form-title">Bienvenido</div>
        <div class="form-sub">Inicia sesión para acceder al panel.</div>

        @if($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('login') }}">
            @csrf
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="admin@asstudio.com"
                       required autofocus>
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="••••••••" required>
            </div>

            <div class="remember-row">
                <input type="checkbox" id="remember" name="remember">
                <span>Recordarme</span>
                {{-- <a href="{{ route('password.request') }}" class="forgot-link">¿Olvidaste tu contraseña?</a> --}}
            </div>

            <button type="submit" class="btn-submit">Iniciar sesión</button>
        </form>

        <div class="bottom-links">
            <span>¿No tienes cuenta?
                <a href="{{ route('register', ['redirect' => request('redirect')]) }}">Regístrate</a>
            </span>
        </div>
    </div>
</div>

</body>
</html>
