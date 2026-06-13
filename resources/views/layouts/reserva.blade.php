<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>AStudio - Reserva</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/styles.css') }}">
    @stack('styles')
</head>
<body>

<div class="reserva-wrapper">

    {{-- Columna izquierda: carrusel --}}
    <div class="reserva-carousel">
        <div id="carruselReserva" class="carousel slide carousel-fade h-100" data-bs-ride="carousel">
            <div class="carousel-inner h-100">

                <div class="carousel-item active">
                    <img src="{{ asset('images/medium/grad.webp') }}" alt="Foto 1">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('images/medium/mate.webp') }}" alt="Foto 3">
                </div>

                <div class="carousel-item">
                    <img src="{{ asset('images/medium/grad2.webp') }}" alt="Foto 4">
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
