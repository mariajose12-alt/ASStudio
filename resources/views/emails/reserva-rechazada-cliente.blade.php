@extends('layouts.email')

@section('email_title', 'Reserva Rechazada')

@section('content')

    <h2 style="color:#c62828;">Tu reserva no pudo ser aprobada</h2>
    <p>Hola <strong>{{ $reserva->cliente->usuario->persona->nombre }} {{ $reserva->cliente->usuario->persona->apellido }}</strong>, lamentablemente tu solicitud no pudo ser aceptada.</p>

    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="padding:10px; background:#f5f5f5; width:140px;"><strong>Paquete</strong></td>
            <td style="padding:10px;">{{ $reserva->paquete->nombre }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Fecha solicitada</strong></td>
            <td style="padding:10px;">
                {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i') }}
            </td>
        </tr>
        @if($reserva->motivo_rechazo)
            <tr>
                <td style="padding:10px; background:#f5f5f5;"><strong>Motivo</strong></td>
                <td style="padding:10px;">{{ $reserva->motivo_rechazo }}</td>
            </tr>
        @endif
    </table>

    <p style="margin-top:20px;">
        Puedes intentar reservar en otra fecha disponible.
    </p>

    <a href="{{ url('/cliente/reservas/paso1') }}"
       style="display:inline-block; margin-top:16px; padding:12px 28px;
                  background:#f59e0b; color:#fff; border-radius:8px;
                  text-decoration:none; font-weight:bold;">
        Hacer nueva reserva →
    </a>

    <p style="margin-top:32px; font-size:12px; color:#999;">
        AS Studio · Mensaje automático, por favor no responder.
    </p>
@endsection
