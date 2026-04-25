@extends('layouts.app')
@section('title', 'Mi Perfil')

@section('content')
    <div style="display:flex; flex-direction:column; gap:1.5rem;">

        <div class="card">
            <div class="card-header">
                <h2>Información Personal</h2>
                <p style="margin:0; font-size:0.85rem; color:#888; font-weight:400;">Actualiza tu nombre y correo electrónico.</p>
            </div>
            {{--
            <div class="card-body">
                @include('profile.partials.update-profile-information-form')
            </div>
            --}}
        </div>

        <div class="card">
            <div class="card-header">
                <h2>Cambiar Contraseña</h2>
                <p style="margin:0; font-size:0.85rem; color:#888; font-weight:400;">Usa una contraseña larga y segura.</p>
            </div>
            {{--
            <div class="card-body">
                @include('profile.partials.update-password-form')
            </div>
            --}}
        </div>

        <div class="card" style="border-color:#fca5a5;">
            <div class="card-header" style="background:#fff5f5; border-color:#fca5a5;">
                <h2 style="color:#dc2626;">Zona de Peligro</h2>
                <p style="margin:0; font-size:0.85rem; color:#f87171; font-weight:400;">Una vez eliminada tu cuenta, no hay vuelta atrás.</p>
            </div>
            {{--
            <div class="card-body">
                @include('profile.partials.delete-user-form')
            </div>
            --}}
        </div>

    </div>
@endsection
