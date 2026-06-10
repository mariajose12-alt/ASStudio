@extends('layouts.email')

@section('email_title', 'Galería Disponible')

@section('content')
    <h2 style="color:#e87722;">
        @if($tipo === 'seleccion')
            Tu galería está lista para selección
        @else
            Tu galería final está lista
        @endif
    </h2>

    <p>Hola <strong>{{ $sesion->reserva->cliente->usuario->persona->nombre }} {{ $sesion->reserva->cliente->usuario->persona->apellido }}</strong>,
        @if($tipo === 'seleccion')
            tu fotógrafo ya subió las fotografías de tu sesión. ¡Ingresa y elige tus favoritas!
        @else
            tus fotos editadas ya están listas. ¡Ingresa y descárgalas!
        @endif
    </p>

    <table style="width:100%; border-collapse:collapse; margin-top:16px;">
        <tr>
            <td style="padding:10px; background:#f5f5f5; width:140px;"><strong>Paquete</strong></td>
            <td style="padding:10px;">{{ $sesion->reserva->paquete->nombre }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Fecha de sesión</strong></td>
            <td style="padding:10px;">{{ \Carbon\Carbon::parse($sesion->fecha_inicio)->format('d/m/Y') }}</td>
        </tr>
        <tr>
            <td style="padding:10px; background:#f5f5f5;"><strong>Lugar</strong></td>
            <td style="padding:10px;">{{ $sesion->lugar }}</td>
        </tr>
    </table>

    <div style="background:#fff3e8; border-left:4px solid #e87722;
            padding:12px 16px; border-radius:0 8px 8px 0; margin-top:20px;">
        <strong>Próximo paso:</strong>
        @if($tipo === 'seleccion')
            Ingresa a tu galería, revisa todas las fotos y confirma tu selección final.
        @else
            Ingresa a tu galería final y descarga tus fotos editadas.
        @endif
    </div>

    <a href="{{ url('/cliente/galeria') }}"
       style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#e87722; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
        Ver mi galería →
    </a>
@endsection
