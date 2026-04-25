<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio — Crear Cuenta</title>
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

        .panel-left {
            background: #1a1612;
            padding: 44px 36px;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            min-height: 600px;
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

        .left-bottom { font-size: 11px; color: #4a4030; }

        .panel-right {
            padding: 44px;
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
            margin-bottom: 28px;
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 14px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 6px;
            margin-bottom: 16px;
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

        /* Barra de fortaleza de contraseña */
        .strength-wrap { margin-top: 6px; }

        .strength-bar {
            height: 3px;
            background: #e8e4dc;
            border-radius: 3px;
            overflow: hidden;
        }

        .strength-fill {
            height: 100%;
            width: 0%;
            border-radius: 3px;
            transition: width 0.3s, background 0.3s;
        }

        .strength-label {
            font-size: 10px;
            color: #9a9488;
            margin-top: 4px;
        }

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
            margin-top: 4px;
        }

        .btn-submit:hover { background: #d4a843; }

        .terms {
            font-size: 11px;
            color: #bbb;
            text-align: center;
            margin-top: 14px;
            line-height: 1.7;
        }

        .terms a { color: #b8922a; text-decoration: none; }
        .terms a:hover { text-decoration: underline; }

        .bottom-links {
            text-align: center;
            margin-top: 16px;
            font-size: 13px;
            color: #9a9488;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }

        .bottom-links a { color: #b8922a; text-decoration: none; font-weight: 500; }
        .bottom-links a:hover { text-decoration: underline; }
        .back-link { color: #9a9488 !important; font-weight: 400 !important; }

        .error-box {
            background: #fef5f4;
            border: 1px solid #f0c8c5;
            color: #c0392b;
            padding: 11px 15px;
            border-radius: 9px;
            font-size: 13px;
            margin-bottom: 18px;
        }

        .field-error { font-size: 11px; color: #c0392b; margin-top: 2px; }

        @media (max-width: 640px) {
            .auth-card { grid-template-columns: 1fr; }
            .panel-left { display: none; }
            .form-row { grid-template-columns: 1fr; }
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
                Únete al equipo que <em>transforma</em> momentos en arte.
            </div>
            <div class="left-cite">Nuevo usuario</div>
        </div>
        <div class="left-bottom">© {{ date('Y') }} AS Studio</div>
    </div>

    <!-- Formulario -->
    <div class="panel-right">
        <div class="form-title">Crear cuenta</div>
        <div class="form-sub">Completa tus datos para registrarte.</div>

        @if($errors->any())
            <div class="error-box">{{ $errors->first() }}</div>
        @endif

        <form method="POST" action="{{ route('register') }}">
            @csrf

            <div class="form-row">
                <div class="form-group">
                    <label for="nombre">Nombre</label>
                    <input type="text" id="nombre" name="nombre"
                           value="{{ old('nombre') }}"
                           placeholder="Juan" required autofocus>
                    @error('nombre')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>
                <div class="form-group">
                    <label for="apellido">Apellido</label>
                    <input type="text" id="apellido" name="apellido"
                           value="{{ old('apellido') }}"
                           placeholder="Pérez">
                </div>
            </div>

            <div class="form-group">
                <label for="telefono">Teléfono <span style="color:#bbb">(opcional)</span></label>
                <input type="text" id="telefono" name="telefono"
                       value="{{ old('telefono') }}"
                       placeholder="+1 809 000 0000">
                @error('telefono')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="juan@asstudio.com" required>
                @error('email')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password">Contraseña</label>
                <input type="password" id="password" name="password"
                       placeholder="Mínimo 8 caracteres" required
                       oninput="updateStrength(this.value)">
                <div class="strength-wrap">
                    <div class="strength-bar">
                        <div class="strength-fill" id="strength-fill"></div>
                    </div>
                    <div class="strength-label" id="strength-label"></div>
                </div>
                @error('password')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="password_confirmation">Confirmar contraseña</label>
                <input type="password" id="password_confirmation"
                       name="password_confirmation"
                       placeholder="Repite tu contraseña" required>
            </div>

            <button type="submit" class="btn-submit">Crear cuenta</button>
        </form>

        <div class="terms">
            Al registrarte aceptas los
            <a href="#">términos de uso</a> y la
            <a href="#">política de privacidad</a>.
        </div>

        <div class="bottom-links">
            <span>¿Ya tienes cuenta? <a href="{{ route('login') }}">Inicia sesión</a></span>
            <a href="{{ url('/') }}" class="back-link">← Volver al sitio</a>
        </div>
    </div>
</div>

<script>
    function updateStrength(val) {
        const fill  = document.getElementById('strength-fill');
        const label = document.getElementById('strength-label');
        let score = 0;
        if (val.length >= 8)            score++;
        if (/[A-Z]/.test(val))          score++;
        if (/[0-9]/.test(val))          score++;
        if (/[^A-Za-z0-9]/.test(val))   score++;

        const levels = [
            { w: '0%',   color: '#e8e4dc', text: '' },
            { w: '33%',  color: '#c0392b', text: 'Débil' },
            { w: '60%',  color: '#b8922a', text: 'Regular' },
            { w: '80%',  color: '#7aaa6a', text: 'Buena' },
            { w: '100%', color: '#2e7d52', text: 'Fuerte' },
        ];

        const lvl = val.length === 0 ? levels[0] : levels[Math.min(score, 4)];
        fill.style.width     = lvl.w;
        fill.style.background = lvl.color;
        label.textContent    = lvl.text;
    }
</script>

</body>
</html>
