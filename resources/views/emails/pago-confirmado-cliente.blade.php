@extends('layouts.email')

@section('email_title', 'Confirmación de Pago')

@section('content')
    <h2 style="color:#2e7d32;">✓ Pago recibido — sesión confirmada</h2>

    <p>Hola <strong>{{ $reserva->cliente->usuario->persona->nombre }} {{ $reserva->cliente->usuario->persona->apellido }}</strong>,
        hemos recibido tu pago correctamente. Tu sesión fotográfica queda confirmada.</p>

    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="padding:10px; background:#f5f5f5; width:140px;"><strong>Paquete</strong></td>
            <td style="padding:10px;">{{ $reserva->paquete->nombre }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Fecha</strong></td>
            <td style="padding:10px;">{{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i') }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Tipo</strong></td>
            <td style="padding:10px;">{{ $reserva->tipo }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Total pagado</strong></td>
            <td style="padding:10px;">${{ number_format($reserva->precio_total, 0, ',', '.') }}</td>
        </tr>
    </table>

    <div style="background:#e8f5e9; border-left:4px solid #2e7d32;
                padding:12px 16px; border-radius:0 8px 8px 0; margin-top:20px;">
        <strong>¡Todo listo!</strong> Tu fotógrafo se pondrá en contacto contigo antes de la sesión.
    </div>

    <a href="{{ url('/cliente/reservas') }}"
       style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#f59e0b; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
        Ver mis reservas →
    </a>

    <p style="margin-top:32px; font-size:12px; color:#999;">
        AS Studio · Mensaje automático, por favor no responder.
    </p>
@endsection
