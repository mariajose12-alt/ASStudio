<!DOCTYPE html>
<html>
<body style="font-family:sans-serif; color:#333; padding:20px; max-width:600px; margin:auto;">

<h2 style="color:#2e7d32;">¡Tu reserva fue aprobada! ✓</h2>
<p>Hola <strong>{{ $reserva->cliente->usuario->persona->nombre }} {{ $reserva->cliente->usuario->persona->apellido }} </strong>, tu sesión fotográfica ha sido confirmada.</p>

<table style="width:100%; border-collapse:collapse; margin-top:16px;">
    <tr>
        <td style="padding:10px; background:#f5f5f5; width:140px;"><strong>Paquete</strong></td>
        <td style="padding:10px;">{{ $reserva->paquete->nombre }}</td>
    </tr>
    <tr>
        <td style="padding:10px; background:#f5f5f5;"><strong>Fecha</strong></td>
        <td style="padding:10px;">
            {{ \Carbon\Carbon::parse($reserva->fecha_inicio)->format('d/m/Y H:i') }}
        </td>
    </tr>
    <tr>
        <td style="padding:10px; background:#f5f5f5;"><strong>Tipo</strong></td>
        <td style="padding:10px;">{{ $reserva->tipo }}</td>
    </tr>
    <tr>
        <td style="padding:10px; background:#f5f5f5;"><strong>Precio</strong></td>
        <td style="padding:10px;">${{ number_format($reserva->precio_total, 0, ',', '.') }}</td>
    </tr>
</table>

<div style="background:#fff3cd; border-left:4px solid #f59e0b;
                padding:12px 16px; border-radius:0 8px 8px 0; margin-top:20px;">
    <strong>Próximo paso:</strong> Debes realizar el pago para confirmar tu sesión.
    Tienes un plazo establecido para completarlo.
</div>

<a href="{{ url('/cliente/reservas') }}"
   style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#f59e0b; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
    Ir a mis reservas →
</a>

<p style="margin-top:32px; font-size:12px; color:#999;">
    AS Studio · Mensaje automático, por favor no responder.
</p>
</body>
</html>
