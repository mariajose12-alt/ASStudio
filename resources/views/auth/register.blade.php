<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio — Crear Cuenta</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:ital,wght@0,400;0,600;1,400&family=DM+Sans:wght@300;400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body class="layout-auth">

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
            <span>¿Ya tienes cuenta?
                <a href="{{ route('login', ['redirect' => request('redirect')]) }}">Inicia sesión</a>
            </span>
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
