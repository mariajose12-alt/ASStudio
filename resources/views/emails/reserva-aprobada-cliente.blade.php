@extends('layouts.email')

@section('email_title', 'Reserva aprobada')

@section('content')

    <h1 style="
    margin:0 0 16px;
    font-size:28px;
    font-weight:700;
    color:#1a0d00;
">
        ¡Tu reserva ha sido aprobada!
    </h1>

    <p style="
    margin:0 0 24px;
    font-size:15px;
    line-height:1.7;
    color:#4b5563;
">
        Hola {{ $reserva->cliente->usuario->persona->nombre }},
        nos complace informarte que tu solicitud de reserva fue aprobada.
    </p>

    <div class="email-info-block" style="margin-top:0;">
        <p class="email-info-block__title">
            Detalles de la reserva
        </p>

        <div class="email-info-grid">
            <div class="email-info-item">
                <span class="email-info-item__label">Paquete</span>
                <span class="email-info-item__value">
                {{ $reserva->paquete->nombre }}
            </span>
            </div>

            <div class="email-info-item">
                <span class="email-info-item__label">Fecha</span>
                <span class="email-info-item__value">
                {{ $reserva->fecha_inicio->format('d/m/Y') }}
            </span>
            </div>

            <div class="email-info-item">
                <span class="email-info-item__label">Tipo</span>
                <span class="email-info-item__value">
                {{ $reserva->tipo }}
            </span>
            </div>

            <div class="email-info-item">
                <span class="email-info-item__label">Estado</span>
                <span class="email-info-item__value">
                Aprobada
            </span>
            </div>
        </div>
    </div>

    <p style="
    margin:32px 0 24px;
    font-size:15px;
    line-height:1.7;
    color:#4b5563;
">
        Ya puedes acceder a tu reserva y revisar toda la información desde la plataforma.
    </p>

    <div style="text-align:center;">
        <a href="{{ route('cliente.reservas.show', $reserva->id) }}"
           style="
            display:inline-block;
            padding:14px 28px;
            background:#e87722;
            color:#ffffff;
            text-decoration:none;
            border-radius:12px;
            font-weight:600;
       ">
            Ver mi reserva
        </a>
    </div>

@endsection
