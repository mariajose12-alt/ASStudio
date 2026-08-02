@extends('layouts.email')

@section('email_title', 'Bienvenido')

@section('content')
    @php $persona = $usuario->persona; @endphp

    <h2 style="color:#e87722;">¡Bienvenido, {{ $persona->nombre }}!</h2>

    <p>Gracias por unirte. Ya puedes explorar nuestro catálogo, reservar tu sesión
        y llevar el seguimiento de tus fotos desde tu panel de cliente.</p>

    <a href="{{ route('cliente.dashboard') }}"
       style="display:inline-block; margin-top:20px; padding:12px 28px;
              background:#e87722; color:#fff; border-radius:8px;
              text-decoration:none; font-weight:bold;">
        Ir a mi panel →
    </a>

    @if(is_null($usuario->contrasena))
        <div style="margin-top:32px; padding:16px 18px; background:#fff3e8;
                    border-radius:10px; border:1px solid rgba(232,119,34,0.2);">
            <p style="margin:0 0 10px; font-size:14px;">
                Creaste tu cuenta con Google, así que por ahora solo puedes entrar de esa forma.
                Si quieres, puedes además establecer una contraseña para entrar con tu correo
                cuando lo necesites.
            </p>
            <a href="{{ route('cliente.perfil') }}#cambiar-password"
               style="color:#e87722; font-weight:bold; text-decoration:none;">
                Establecer una contraseña →
            </a>
        </div>
    @endif

    <p style="margin-top:24px; font-size:12px; color:#999;">
        Si no creaste esta cuenta, puedes ignorar este correo.
    </p>
@endsection
