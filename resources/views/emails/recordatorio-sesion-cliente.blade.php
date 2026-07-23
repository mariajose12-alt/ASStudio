@extends('layouts.email')

@section('email_title', 'Recordatorio')

@section('content')
    <h2>¡Tu sesión es mañana!</h2>
    <p>Hola {{ $cliente->nombre }},</p>
    <p>
        Te recordamos que tu sesión de fotos con AS Studio está programada para
        <strong>{{ $fecha }}</strong> a las <strong>{{ $hora }}</strong>.
    </p>
    <p>¡Te esperamos!</p>

    <a href="{{ route('cliente.reservas.show', $reserva->id) }}" class="btn">
        Ver detalles de mi reserva
    </a>
@endsection
