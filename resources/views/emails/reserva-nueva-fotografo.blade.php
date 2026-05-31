@extends('layouts.email')

@section('email_title', 'Solicitud de Reserva')

@section('content')

    <h2>Nueva solicitud de reserva</h2>

    <p>Hola {{ $reserva->fotografo->empleado->usuario->persona->nombre }}, tienes una nueva solicitud pendiente de revisión.</p>

    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="padding:8px; background:#f5f5f5;"><strong>Cliente</strong></td>
            <td style="padding:8px;">{{ $reserva->cliente->usuario->persona->nombre }}
                {{ $reserva->cliente->usuario->persona->apellido }}</td>
        </tr>
        <tr>
            <td style="padding:8px; background:#f5f5f5;"><strong>Paquete</strong></td>
            <td style="padding:8px;">{{ $reserva->paquete->nombre }}</td>
        </tr>
        <tr>
            <td style="padding:8px; background:#f5f5f5;"><strong>Fecha</strong></td>
            <td style="padding:8px;">{{ $reserva->fecha_inicio->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="padding:8px; background:#f5f5f5;"><strong>Tipo</strong></td>
            <td style="padding:8px;">{{ $reserva->tipo }}</td>
        </tr>
        <tr>
            <td style="padding:8px; background:#f5f5f5;"><strong>Descripción</strong></td>
            <td style="padding:8px;">{{ $reserva->descripcion }}</td>
        </tr>
    </table>

    <a href="{{ route('fotografo.reservas.show', $reserva->id) }}"
       style="display:inline-block; margin-top:24px; padding:12px 24px;
                  background:#c96015; color:#fff; border-radius:8px;
                  text-decoration:none; font-weight:bold;">
        Ver reserva →
    </a>
@endsection
