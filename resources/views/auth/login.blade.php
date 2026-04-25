<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio — Iniciar Sesión</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'DM Sans', sans-serif;
            background: #f4f2ee;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px;
        }

        .auth-card {
            width: 100%;
            max-width: 860px;
            border-radius: 18px;
            overflow: hidden;
            border: 1px solid #e8e4dc;
            display: grid;
            grid-template-columns: 38% 62%;
            background: #fff;
        }

        /* Panel izquierdo oscuro */
        .panel-left {
            background: #1a1612;
            padding: 44px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 540px;
        }

        .brand {
            font-family: 'Playfair Display', serif;
            font-size: 22px;
            color: #c9a84c;
            letter-spacing: 1.5px;
        }

        .brand-sub {
            font-size: 10px;
            color: #6b5e3e;
            text-transform: uppercase;
            letter-spacing: 3px;
            margin-top: 5px;
        }

        .left-quote {
            font-family: 'Playfair Display', serif;
            font-style: italic;
            font-size: 22px;
            line-height: 1.55;
            color: #c8bfaf;
        }

        .left-quote em {
            color: #c9a84c;
            font-style: normal;
        }

        .left-cite {
            font-size: 10px;
            color: #6b5e3e;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            margin-top: 16px;
        }

        .left-bottom {
            font-size: 11px;
            color: #4a4030;
        }

        /* Panel derecho (formulario) */
        .panel-right {
            padding: 48px 44px;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .form-title {
            font-family: 'Playfair Display', serif;
            font-size: 28px;
            color: #1a1a1a;
            font-weight: 400;
            margin-bottom: 6px;
        }

        .form-sub {
            font-size: 14px;
            color: #9a9488;
            margin-bottom: 32px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 18px;
        }

        label {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1.5px;
            color: #9a9488;
        }

        input[type="email"],
        input[type="password"],
        input[type="text"] {
            background: #fafaf9;
            border: 1px solid #e8e4dc;
            border-radius: 9px;
            color: #1a1a1a;
            padding: 11px 14px;
            font-size: 14px;
            font-family: inherit;
            transition: border-color 0.2s, background 0.2s;
            width: 100%;
        }

        input:focus {
            outline: none;
            border-color: #b8922a;
            background: #fff;
        }

        .remember-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 24px;
        }

        .remember-row input[type="checkbox"] {
            width: 15px;
            accent-color: #b8922a;
        }

        .remember-row span {
            font-size: 13px;
            color: #9a9488;
        }

        .forgot-link {
            margin-left: auto;
            font-size: 12px;
            color: #b8922a;
            text-decoration: none;
            font-weight: 500;
        }

        .forgot-link:hover { text-decoration: underline; }

        .btn-submit {
            width: 100%;
            background: #b8922a;
            color: #fff;
            border: none;
            border-radius: 9px;
            padding: 13px;
            font-size: 14px;
            font-weight: 600;
            font-family: inherit;
            cursor: pointer;
            transition: background 0.2s;
            letter-spacing: 0.3px;
        }

        .btn-submit:hover { background: #d4a843; }

        .bottom-links {
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #9a9488;
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .bottom-links a {
            color: #b8922a;
            text-decoration: none;
            font-weight: 500;
        }

        .bottom-links a:hover { text-decoration: underline; }

        .back-link { color: #9a9488 !important; font-weight: 400 !important; }

        .error-box {
            background: #fef5f4;
            border: 1px solid #f0c8c5;
            color: #c0392b;
            padding: 11px 15px;
            border-radius: 9px;
            font-size: 13px;
            margin-bottom: 20px;
        }

        @media (max-width: 640px) {
            .auth-card { grid-template-columns: 1fr; }
            .panel-left { display: none; }
        }
    </style>
</head>
<body>

<div class="auth-card">
    <!-- Panel izquierdo -->
    <div class="panel-left">
        <div>
            <div class="brand">AS Studio</div>
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
            <span>¿No tienes cuenta? <a href="{{ route('register') }}">Regístrate</a></span>
            <a href="{{ url('/') }}" class="back-link">← Volver al sitio</a>
        </div>
    </div>
</div>

</body>
</html>
