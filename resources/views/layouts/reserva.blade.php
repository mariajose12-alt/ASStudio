<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio - Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            height: 100vh;
            overflow: hidden;
            font-family: 'Inter', sans-serif;
        }

        .reserva-wrapper {
            display: flex;
            height: 100vh;
        }

        /* ── Columna carrusel ── */
        .reserva-carousel {
            width: 40%;
            height: 100vh;
            position: relative;
            overflow: hidden;
        }

        .reserva-carousel .carousel,
        .reserva-carousel .carousel-inner,
        .reserva-carousel .carousel-item {
            height: 100%;
        }

        .reserva-carousel .carousel-item img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
        }

        .reserva-carousel::after {
            content: '';
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.3);
            pointer-events: none;
        }

        /* ── Columna formulario ── */
        .reserva-form {
            width: 60%;
            height: 100vh;
            overflow-y: auto;
            padding: 3rem 3.5rem;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background: #fff;
        }

        /* Logo arriba */
        .reserva-logo {
            font-size: 1rem;
            font-weight: 700;
            letter-spacing: 0.15em;
            color: #111;
            text-transform: uppercase;
            margin-bottom: 2.5rem;
        }

        .reserva-logo span {
            color: #E07B2A;
        }

        /* Títulos */
        .reserva-form h5 {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.12em;
            color: #E07B2A;
            text-transform: uppercase;
            margin-bottom: 0.4rem;
        }

        .reserva-form h4 {
            font-size: 1.5rem;
            font-weight: 700;
            color: #111;
            margin-bottom: 2rem;
        }

        /* ── Stepper ── */
        .stepper {
            display: flex;
            align-items: center;
            margin-bottom: 2.5rem;
        }

        .stepper-step {
            display: flex;
            flex-direction: column;
            align-items: center;
            position: relative;
        }

        .stepper-circle {
            width: 36px;
            height: 36px;
            border-radius: 50%;
            border: 2px solid #ddd;
            background: #fff;
            color: #aaa;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.85rem;
            font-weight: 600;
            transition: all 0.3s ease;
            z-index: 1;
        }

        .stepper-circle.active {
            border-color: #E07B2A;
            background: #E07B2A;
            color: #fff;
            box-shadow: 0 0 0 4px rgba(224, 123, 42, 0.15);
        }

        .stepper-circle.done {
            border-color: #E07B2A;
            background: #fff;
            color: #E07B2A;
        }

        .stepper-label {
            font-size: 0.65rem;
            color: #aaa;
            margin-top: 0.35rem;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            white-space: nowrap;
        }

        .stepper-label.active {
            color: #E07B2A;
        }

        .stepper-line {
            flex: 1;
            height: 2px;
            background: #eee;
            margin: 0 0.5rem;
            margin-bottom: 1.2rem;
            transition: background 0.3s ease;
        }

        .stepper-line.done {
            background: #E07B2A;
        }

        /* ── Inputs borde inferior ── */
        .form-floating-modern {
            position: relative;
            margin-bottom: 1.6rem;
        }

        .form-floating-modern label {
            font-size: 0.75rem;
            font-weight: 600;
            letter-spacing: 0.08em;
            text-transform: uppercase;
            color: #999;
            margin-bottom: 0.4rem;
            display: block;
        }

        .form-floating-modern input,
        .form-floating-modern select,
        .form-floating-modern textarea {
            width: 100%;
            border: none;
            border-bottom: 1.5px solid #ddd;
            border-radius: 0;
            padding: 0.6rem 0;
            font-size: 0.95rem;
            color: #111;
            background: transparent;
            outline: none;
            transition: border-color 0.2s ease;
            appearance: auto;
        }

        .form-floating-modern input:focus,
        .form-floating-modern select:focus,
        .form-floating-modern textarea:focus {
            border-bottom-color: #E07B2A;
            box-shadow: none;
        }

        .form-floating-modern input.is-invalid,
        .form-floating-modern select.is-invalid,
        .form-floating-modern textarea.is-invalid {
            border-bottom-color: #dc3545;
        }

        .form-floating-modern .invalid-feedback {
            display: block;
            font-size: 0.75rem;
            color: #dc3545;
            margin-top: 0.3rem;
        }

        /* Radio buttons */
        .radio-group {
            display: flex;
            gap: 1rem;
            margin-bottom: 1.6rem;
        }

        .radio-option {
            flex: 1;
            border: 1.5px solid #ddd;
            border-radius: 8px;
            padding: 0.75rem 1rem;
            cursor: pointer;
            transition: all 0.2s ease;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.9rem;
            color: #555;
        }

        .radio-option:has(input:checked) {
            border-color: #E07B2A;
            background: rgba(224, 123, 42, 0.05);
            color: #E07B2A;
            font-weight: 600;
        }

        .radio-option input {
            accent-color: #E07B2A;
        }

        /* Botón */
        .btn-reserva {
            background: #E07B2A;
            color: #fff;
            border: none;
            padding: 0.75rem 2rem;
            font-size: 0.9rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            border-radius: 6px;
            cursor: pointer;
            transition: background 0.2s ease;
        }

        .btn-reserva:hover {
            background: #c96a1e;
            color: #fff;
        }

        /* Scrollbar del formulario */
        .reserva-form::-webkit-scrollbar { width: 4px; }
        .reserva-form::-webkit-scrollbar-track { background: transparent; }
        .reserva-form::-webkit-scrollbar-thumb { background: #eee; border-radius: 2px; }

        /* Responsive */
        @media (max-width: 768px) {
            .reserva-carousel { display: none; }
            .reserva-form { width: 100%; padding: 2rem 1.5rem; }
        }
    </style>
</head>
<body>

<div class="reserva-wrapper">

    {{-- Columna izquierda: formulario --}}
    <div class="reserva-carousel">
        <div id="carruselReserva" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
            <div class="carousel-inner h-100">

                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1773609108583-4f0040c75e7f?q=80&w=1632&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Foto 1">
                </div>

                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1772442164791-df481a73ce0e?q=80&w=735&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Foto 2">
                </div>

                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1542362567-b07e54358753?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Foto 3">
                </div>

                <div class="carousel-item">
                    <img src="https://images.unsplash.com/photo-1773613007146-650c070ffc59?q=80&w=1470&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Foto 4">
                </div>

            </div>
        </div>
    </div>

    {{-- Columna derecha: carrusel --}}
    <div class="reserva-form">
        @yield('formulario')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Scripts adicionales por página --}}
@stack('scripts')

</body>
</html>
