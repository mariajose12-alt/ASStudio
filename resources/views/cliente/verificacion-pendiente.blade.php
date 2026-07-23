<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Olvidé mi contraseña · {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&family=Arial&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@tabler/icons-webfont@3.x.x/dist/tabler-icons.min.css">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('css/galeria.css') }}">
</head>
<body>
    <div class="vp-page">
        <div class="vp-card">

            <div class="vp-icon-banner">
                <svg width="30" height="30" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round">
                    <path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/>
                    <polyline points="22,6 12,13 2,6"/>
                </svg>
            </div>

            <p class="vp-eyebrow">Un paso más</p>
            <h1 class="vp-titulo">Verifica tu correo</h1>

            <p class="vp-texto">
                Enviamos un enlace de verificación a
            </p>
            <div class="vp-email-pill">{{ auth()->user()->email }}</div>
            <p class="vp-texto">
                Revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
            </p>

            @if(session('success'))
                <div class="vp-alerta vp-alerta--ok">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 11.08V12a10 10 0 11-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="vp-alerta vp-alerta--error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    {{ session('error') }}
                </div>
            @endif

            <form method="POST" action="{{ route('cliente.verificacion.reenviar') }}">
                @csrf
                <button type="submit" class="vp-btn-primario">
                    Reenviar correo de verificación
                </button>
            </form>

            <p class="vp-hint">
                ¿No lo encuentras? Revisa también tu carpeta de spam o correo no deseado.
            </p>

            <div class="vp-divider"></div>

            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="vp-link-cerrar">
                    Cerrar sesión
                </button>
            </form>

        </div>
    </div>


    <style>
        .vp-page {
            min-height: calc(100vh - 80px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 24px 16px;
        }

        .vp-card {
            width: 100%;
            max-width: 440px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            padding: 40px 32px 32px;
            text-align: center;
            box-shadow: 0 4px 24px rgba(26,15,0,0.06);
        }

        .vp-icon-banner {
            width: 64px;
            height: 64px;
            margin: 0 auto 20px;
            border-radius: 50%;
            background: #fff3e8;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #e87722;
        }

        .vp-eyebrow {
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: #e87722;
            margin: 0 0 6px;
        }

        .vp-titulo {
            font-family: var(--font-serif);
            font-size: 1.5rem;
            font-weight: 600;
            color: var(--ink);
            margin: 0 0 16px;
        }

        .vp-texto {
            font-size: 0.88rem;
            color: var(--muted);
            line-height: 1.6;
            margin: 0 0 10px;
        }

        .vp-email-pill {
            display: inline-block;
            background: var(--snow);
            border: 1px solid var(--border);
            border-radius: 999px;
            padding: 6px 16px;
            font-size: 0.85rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 10px;
            word-break: break-all;
        }

        .vp-alerta {
            display: flex;
            align-items: center;
            gap: 8px;
            justify-content: center;
            border-radius: 10px;
            padding: 12px 16px;
            font-size: 0.82rem;
            margin: 20px 0 4px;
            text-align: left;
        }
        .vp-alerta--ok    { background: #e9f7ef; color: #1e7e44; }
        .vp-alerta--error { background: #fdecea; color: #9a2a1c; }
        .vp-alerta svg { flex-shrink: 0; }

        .vp-btn-primario {
            width: 100%;
            background: #e87722;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 14px;
            font-size: 0.9rem;
            font-weight: 700;
            cursor: pointer;
            margin-top: 24px;
            transition: opacity 0.2s, transform 0.15s;
        }
        .vp-btn-primario:hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        .vp-hint {
            font-size: 0.75rem;
            color: var(--muted);
            margin: 14px 0 0;
        }

        .vp-divider {
            height: 1px;
            background: var(--border);
            margin: 24px 0 16px;
        }

        .vp-link-cerrar {
            background: none;
            border: none;
            color: var(--muted);
            font-size: 0.8rem;
            cursor: pointer;
            text-decoration: underline;
        }
        .vp-link-cerrar:hover {
            color: var(--ink);
        }
    </style>
</body>
</html>
