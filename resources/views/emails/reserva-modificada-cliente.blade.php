<!DOCTYPE html>
<html>
<body style="font-family:sans-serif; color:#333; padding:20px; max-width:600px; margin:auto;">

<h2 style="color:#e65100;">El fotógrafo propone un cambio</h2>
<p>Hola <strong>{{ $reserva->cliente->usuario->persona->nombre }} {{ $reserva->cliente->usuario->persona->apellido }}</strong>,
    el fotógrafo ha propuesto una modificación a tu reserva.</p>

<table style="width:100%; border-collapse:collapse; margin-top:16px;">
    <tr>
        <td style="padding:10px; background:#f5f5f5; width:140px;"><strong>Paquete</strong></td>
        <td style="padding:10px;">{{ $reserva->paquete->nombre }}</td>
    </tr>
    <tr>
        <td style="padding:10px; background:#f5f5f5;"><strong>Nueva idea propuesta</strong></td>
        <td style="padding:10px;">
            {{ $reserva->motivo_rechazo }}
        </td>
    </tr>
</table>

<a href="{{ url('/cliente/reservas') }}"
   style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#f59e0b; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
    Ver mi reserva →
</a>

<p style="margin-top:32px; font-size:12px; color:#999;">
    AS Studio · Mensaje automático, por favor no responder.
</p>
</body>
</html>
