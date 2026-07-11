@extends('layouts.cliente')
@section('title', 'Verifica tu cuenta')

@section('content')
    <div style="max-width:500px; margin:80px auto; text-align:center;">
        <div style="font-size:48px; margin-bottom:24px;">✉️</div>
        <h2 style="font-family:'Playfair Display', serif; margin-bottom:12px;">Verifica tu correo</h2>
        <p style="color:var(--muted); line-height:1.6; margin-bottom:32px;">
            Enviamos un enlace de verificación a <strong>{{ auth()->user()->email }}</strong>.
            Revisa tu bandeja de entrada y haz clic en el enlace para activar tu cuenta.
        </p>

        @if(session('success'))
            <div style="background:#d1fae5; color:#065f46; padding:12px 16px; border-radius:8px; margin-bottom:20px;">
                {{ session('success') }}
            </div>
        @endif

        <form method="POST" action="{{ route('cliente.verificacion.reenviar') }}">
            @csrf
            <button type="submit" class="btn-reserva" style="width:100%;">
                Reenviar correo de verificación
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" style="margin-top:16px;">
            @csrf
            <button type="submit" style="background:none; border:none; color:var(--muted); font-size:13px; cursor:pointer; text-decoration:underline;">
                Cerrar sesión
            </button>
        </form>
    </div>
@endsection
