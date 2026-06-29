@extends('layouts.email')

@section('email_title', 'Nómina Disponible')

@section('content')
    @php
        $persona = $detalle->fotografo->empleado->usuario->persona;
    @endphp

    <h2 style="color:#e87722;">Tu nómina está lista para revisión</h2>

    <p>Hola <strong>{{ $persona->nombre }} {{ $persona->apellido }}</strong>,
        ya se calculó tu nómina del período <strong>{{ $detalle->nomina->periodo }}</strong>. Ingresa a tu cuenta para revisar el desglose completo y confirmar si todo está correcto.</p>

    <div style="background:#fff3e8; border-left:4px solid #e87722;
                padding:12px 16px; border-radius:0 8px 8px 0; margin-top:20px;">
        <strong>Próximo paso:</strong> Inicia sesión y entra al apartado de Nómina para ver tu desglose y confirmar tu pago.
    </div>

    <a href="{{ route('login') }}"
       style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#e87722; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
        Iniciar sesión →
    </a>
@endsection
