<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AS Studio - Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
</head>
<body>

<div class="reserva-wrapper">

    {{-- Columna izquierda: carrusel --}}
    <div class="reserva-carousel">
        <div id="carruselReserva" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
            <div class="carousel-inner h-100">

                <div class="carousel-item active">
                    <img src="https://images.unsplash.com/photo-1773609108583-4f0040c75e7f?q=80&w=1632&auto=format&fit=crop&ixlib=rb-4.1.0&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D" alt="Foto 1">
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

    {{-- Columna derecha: formulario --}}
    <div class="reserva-form">
        @yield('formulario')
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

{{-- Scripts adicionales por página --}}
@stack('scripts')

</body>
</html>
