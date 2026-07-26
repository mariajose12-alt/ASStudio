<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Crear Cuenta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="layout-auth">

<div class="auth-card">
    <!-- Panel izquierdo -->
    <div class="panel-left">
        <div>
            <div>
                <div style="filter: brightness(0) invert(1);">
                    <a href="/" class="navbar-logo">
                        <img src="{{ asset('images/logo.png') }}" alt="Abraham Sánchez" height="45">
                    </a>
                </div>
                <div class="brand-sub">Fotografía profesional</div>
            </div>
        </div>
        <div>
            <div class="left-quote">
                Únete al equipo que <em>transforma</em> momentos en arte.
            </div>
            <div class="left-cite">Nuevo usuario</div>
        </div>
        <div class="left-bottom">© {{ date('Y') }} Abraham Sánchez</div>
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
            <input type="hidden" name="redirect" value="{{ request('redirect') }}">

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
                       placeholder="809 000 0000">
                @error('telefono')
                <span class="field-error">{{ $message }}</span>
                @enderror
            </div>

            <div class="form-group">
                <label for="email">Correo electrónico</label>
                <input type="email" id="email" name="email"
                       value="{{ old('email') }}"
                       placeholder="nombre@ejemplo.com" required>
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
            <a href="#" onclick="event.preventDefault(); openTermsModal();">términos de uso</a> y la
            <a href="#" onclick="event.preventDefault(); openPrivacyModal();">política de privacidad</a>
        </div>

        <div class="bottom-links">
            <span>¿Ya tienes cuenta?
                <a href="{{ route('login', ['redirect' => request('redirect')]) }}">Inicia sesión</a>
            </span>
        </div>

        <div class="divider">
            <span>o continúa con</span>
        </div>

        <a href="{{ route('google.redirect') }}" class="btn-google">
            <svg width="18" height="18" viewBox="0 0 48 48">
                <path fill="#EA4335" d="M24 9.5c3.54 0 6.71 1.22 9.21 3.6l6.85-6.85C35.9 2.38 30.47 0 24 0 14.62 0 6.51 5.38 2.56 13.22l7.98 6.19C12.43 13.72 17.74 9.5 24 9.5z"/>
                <path fill="#4285F4" d="M46.98 24.55c0-1.57-.15-3.09-.38-4.55H24v9.02h12.94c-.58 2.96-2.26 5.48-4.78 7.18l7.73 6c4.51-4.18 7.09-10.36 7.09-17.65z"/>
                <path fill="#FBBC05" d="M10.53 28.59c-.48-1.45-.76-2.99-.76-4.59s.27-3.14.76-4.59l-7.98-6.19C.92 16.46 0 20.12 0 24c0 3.88.92 7.54 2.56 10.78l7.97-6.19z"/>
                <path fill="#34A853" d="M24 48c6.48 0 11.93-2.13 15.89-5.81l-7.73-6c-2.18 1.48-4.97 2.31-8.16 2.31-6.26 0-11.57-4.22-13.47-9.91l-7.98 6.19C6.51 42.62 14.62 48 24 48z"/>
                <path fill="none" d="M0 0h48v48H0z"/>
            </svg>
            Continuar con Google
        </a>
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
    <x-terms-modal />
    <x-privacy-modal />
</body>
</html>
