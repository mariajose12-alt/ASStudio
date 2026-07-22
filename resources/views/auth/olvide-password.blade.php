<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Olvidé mi contraseña · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Arial&display=swap" rel="stylesheet">
    <style>
        :root {
            --sage: #a8b5a0;
            --blue-mid: #5b7c99;
            --ink: #1a1a1a;
            --navy: #1a2332;
            --snow: #faf9f6;
        }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--snow);
            color: var(--ink);
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 40px;
            max-width: 420px;
            width: 100%;
        }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            margin: 0 0 8px;
            color: var(--navy);
        }
        p.subtitle {
            font-size: 14px;
            color: #6b6b6b;
            margin: 0 0 28px;
            line-height: 1.6;
        }
        label {
            display: block;
            font-size: 12px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #9a9488;
            margin-bottom: 6px;
        }
        input[type="email"] {
            width: 100%;
            padding: 12px 14px;
            border: 1px solid #e0ddd5;
            border-radius: 8px;
            font-size: 14px;
            margin-bottom: 18px;
        }
        input[type="email"]:focus {
            outline: none;
            border-color: #e87722;
        }
        .btn {
            width: 100%;
            padding: 14px;
            background: #e87722;
            color: #fff;
            border: none;
            border-radius: 999px;
            font-size: 14px;
            font-weight: 700;
            letter-spacing: 0.3px;
            cursor: pointer;
        }
        .btn:hover { background: #d1691c; }
        .error {
            background: #fdecea;
            border: 1px solid rgba(217,48,37,0.25);
            color: #c5341f;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .success {
            background: #eafaf1;
            border: 1px solid rgba(16,185,129,0.25);
            color: #0a7a4c;
            padding: 12px 16px;
            border-radius: 8px;
            font-size: 13px;
            margin-bottom: 20px;
        }
        .volver {
            display: block;
            text-align: center;
            margin-top: 20px;
            font-size: 13px;
            color: #6b6b6b;
            text-decoration: none;
        }
        .volver:hover { color: #e87722; }
    </style>
</head>
<body>
<div class="card">
    <h1>¿Olvidaste tu contraseña?</h1>
    <p class="subtitle">Ingresa tu correo y te enviaremos un enlace para restablecerla.</p>

    @if ($errors->any())
        <div class="error">{{ $errors->first() }}</div>
    @endif
    @if (session('success'))
        <div class="success">{{ session('success') }}</div>
    @endif

    <form method="POST" action="{{ route('password.olvidada.enviar') }}">
        @csrf
        <label for="email">Correo electrónico</label>
        <input type="email" id="email" name="email" required autofocus value="{{ old('email') }}">

        <button type="submit" class="btn">Enviar enlace</button>
    </form>

    <a href="{{ route('login') }}" class="volver">← Volver a iniciar sesión</a>
</div>
</body>
</html>
