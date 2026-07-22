<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Enlace inválido · {{ config('app.name') }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@600;700&display=swap" rel="stylesheet">
    <style>
        :root { --navy: #1a2332; --snow: #faf9f6; }
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: var(--snow);
            color: #1a1a1a;
            display: flex;
            align-items: center;
            justify-content: center;
            min-height: 100vh;
            padding: 20px;
            text-align: center;
        }
        .card {
            background: #fff;
            border-radius: 16px;
            box-shadow: 0 4px 24px rgba(0,0,0,0.06);
            padding: 40px;
            max-width: 420px;
        }
        .icon { font-size: 36px; margin-bottom: 16px; }
        h1 {
            font-family: 'Playfair Display', serif;
            font-size: 24px;
            margin: 0 0 12px;
            color: var(--navy);
        }
        p {
            font-size: 14px;
            color: #6b6b6b;
            line-height: 1.6;
            margin: 0;
        }
    </style>
</head>
<body>
<div class="card">
    <div class="icon">⏱️</div>
    <h1>Este enlace ya no es válido</h1>
    <p>Puede haber expirado o ya fue utilizado. Contacta al administrador para que te envíe un nuevo enlace de activación.</p>
</div>
</body>
</html>
