@extends('layouts.email')

@section('email_title', 'Selección Confirmada')

@section('content')
    <h2 style="color:#1a0f00;">✓ El cliente confirmó su selección</h2>

    <p>Hola {{ $reserva->fotografo->empleado->usuario->persona->nombre }}, el cliente ya eligió sus fotografías favoritas. Puedes proceder con la edición final.</p>

    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="padding:10px; background:#f5f5f5; width:140px;"><strong>Cliente</strong></td>
            <td style="padding:10px;">
                {{ $sesion->reserva->cliente->usuario->persona->nombre }}
                {{ $sesion->reserva->cliente->usuario->persona->apellido }}
            </td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Paquete</strong></td>
            <td style="padding:10px;">{{ $sesion->reserva->paquete->nombre }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Fecha de sesión</strong></td>
            <td style="padding:10px;">{{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d/m/Y') }}</td>
        </tr>
    </table>

    <div style="background:#fff3e8; border-left:4px solid #e87722;
                padding:12px 16px; border-radius:0 8px 8px 0; margin-top:20px;">
        <strong>Siguiente paso:</strong> Edita las fotografías seleccionadas y márcalas como entregadas en el sistema.
    </div>

    <a href="{{ route('fotografo.reservas.show', $sesion->reserva->id) }}"
       style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#f59e0b; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
        Ver reserva →
    </a>
@endsection
