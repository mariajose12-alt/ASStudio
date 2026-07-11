@extends('layouts.email')

@section('email_title', 'Verifica tu cuenta')

@section('content')
    @php $persona = $usuario->persona; @endphp

    <h2 style="color:#e87722;">Verifica tu cuenta</h2>

    <p>Hola <strong>{{ $persona->nombre }} {{ $persona->apellido }}</strong>,
        gracias por registrarte en AS Studio. Solo falta un paso — verifica tu correo haciendo clic en el botón de abajo.</p>

    <a href="{{ route('verificar.email', $usuario->token_verificacion) }}"
       style="display:inline-block; margin-top:24px; padding:12px 28px;
              background:#e87722; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
        Verificar mi cuenta →
    </a>

    <p style="margin-top:24px; font-size:12px; color:#999;">
        Si no creaste esta cuenta, puedes ignorar este correo.
    </p>
@endsection
