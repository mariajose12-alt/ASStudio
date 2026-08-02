@extends('layouts.cliente')
@section('title', 'Mi Perfil')

@section('content')

    {{-- ══════════════════════════════════════════
         CARD DE PERFIL — avatar + datos + badge
         Móvil: centrado, apilado
         Desktop: horizontal con chart de actividad
              integrado (ver .perfil-card en mobile.css)
    ══════════════════════════════════════════ --}}
    <div class="perfil-card">
        <div class="perfil-avatar" aria-hidden="true">
            {{ strtoupper(substr($usuario->persona->nombre, 0, 1)) }}{{ strtoupper(substr($usuario->persona->apellido, 0, 1)) }}
        </div>
        <div class="perfil-info">
            <div class="perfil-nombre">
                {{ $usuario->persona->nombre }} {{ $usuario->persona->apellido }}
            </div>
            <div class="perfil-meta">
                @if($usuario->persona->telefono)
                    <div class="perfil-meta-item">
                        <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
                        </svg>
                        {{ $usuario->persona->telefono }}
                    </div>
                @endif
                <div class="perfil-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                    </svg>
                    {{ $usuario->email }}
                </div>
                <div class="perfil-meta-item">
                    <svg fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                    </svg>
                    Cliente desde {{ $usuario->created_at->format('M Y') }}
                </div>
            </div>
            <div class="perfil-badge">
                <svg width="11" height="11" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                Cuenta activa
            </div>
        </div>
    </div>

    <div class="perfil-facts">
        <div class="perfil-fact">
            <div class="perfil-fact__val">{{ $stats['totalSesiones'] }}</div>
            <div class="perfil-fact__label">Sesiones realizadas</div>
        </div>
        <div class="perfil-fact-divider"></div>
        <div class="perfil-fact">
            <div class="perfil-fact__val" style="{{ $stats['proximaSesion'] ? '' : 'color:var(--text-3); font-size:15px;' }}">
                {{ $stats['proximaSesion'] ? \Carbon\Carbon::parse($stats['proximaSesion'])->translatedFormat('d M') : 'Ninguna' }}
            </div>
            <div class="perfil-fact__label">Próxima sesión</div>
        </div>
        <div class="perfil-fact-divider"></div>
        <div class="perfil-fact">
            <div class="perfil-fact__val">{{ (int) $usuario->created_at->diffInMonths(now()) }}</div>
            <div class="perfil-fact__label">Meses contigo</div>
        </div>
    </div>

    {{-- ══════════════════════════════════════════
         EDITAR DATOS PERSONALES
         Móvil: inputs full-width, áreas táctiles
                de 48px, dos columnas para
                nombre/apellido
         Desktop: card con max-width (ver mobile.css)
    ══════════════════════════════════════════ --}}
    <div class="card">
        <div class="card-header"><h2>Editar datos personales</h2></div>
        <div class="card-body">
            <form method="POST" action="{{ route('cliente.perfil.update') }}">
                @csrf
                @method('PUT')

                <div class="form-row">
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" id="nombre" name="nombre"
                               value="{{ old('nombre', $usuario->persona->nombre) }}"
                               autocomplete="given-name" required>
                        @error('nombre')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                    <div class="form-group">
                        <label for="apellido">Apellido</label>
                        <input type="text" id="apellido" name="apellido"
                               value="{{ old('apellido', $usuario->persona->apellido) }}"
                               autocomplete="family-name" required>
                        @error('apellido')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-group">
                    <label for="telefono">Teléfono</label>
                    <input type="tel" id="telefono" name="telefono"
                           value="{{ old('telefono', $usuario->persona->telefono) }}"
                           autocomplete="tel"
                           placeholder="809 000 0000">
                    @error('telefono')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-group">
                    <label for="email">Correo electrónico</label>
                    <input type="email" id="email" name="email"
                           value="{{ old('email', $usuario->email) }}"
                           autocomplete="email" required>
                    @error('email')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                {{-- Móvil: botones full-width apilados.
                     Desktop: en línea (ver .form-actions abajo) --}}
                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar cambios</button>
                    <a href="{{ route('cliente.perfil') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>
        </div>
    </div>
    {{-- ══════════════════════════════════════════
         CAMBIAR CONTRASEÑA
    ══════════════════════════════════════════ --}}
    @php $tieneContrasena = ! is_null(auth()->user()->contrasena); @endphp
    <div class="card" id="cambiar-password">
        <div class="card-header"><h2>{{ $tieneContrasena ? 'Cambiar contraseña' : 'Establecer contraseña' }}</h2></div>
        <div class="card-body">

            @if (session('status') === 'password-updated')
                <div class="alert-success">
                    <svg width="16" height="16" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true">
                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                    </svg>
                    Contraseña actualizada correctamente.
                </div>
            @endif

            @if ($errors->updatePassword->any())
                <div class="alert-error">
                    Revisa los campos marcados abajo.
                </div>
            @endif

            @if (! $tieneContrasena)
                <p style="font-size:13px; color:var(--muted); margin-bottom:16px;">
                    Tu cuenta usa Google para iniciar sesión. Puedes establecer una contraseña
                    para además poder entrar con tu correo, sin depender de Google.
                </p>
            @endif

            <form method="POST" action="{{ route('password.update') }}">
                @csrf
                @method('PUT')

                @if ($tieneContrasena)
                    <div class="form-group">
                        <label for="current_password">Contraseña actual</label>
                        <input type="password" id="current_password" name="current_password"
                               autocomplete="current-password" required>
                        @error('current_password', 'updatePassword')
                        <span class="field-error">{{ $message }}</span>
                        @enderror
                    </div>
                @endif

                <div class="form-group">
                    <label for="password">{{ $tieneContrasena ? 'Nueva contraseña' : 'Contraseña' }}</label>
                    <input type="password" id="password" name="password"
                           autocomplete="new-password" required>
                    @error('password', 'updatePassword')
                    <span class="field-error">{{ $message }}</span>
                    @enderror
                </div>

                <div class="form-group">
                    <label for="password_confirmation">Confirmar {{ $tieneContrasena ? 'nueva ' : '' }}contraseña</label>
                    <input type="password" id="password_confirmation" name="password_confirmation"
                           autocomplete="new-password" required>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">
                        {{ $tieneContrasena ? 'Actualizar contraseña' : 'Establecer contraseña' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Al final, después de la card de "Cambiar contraseña" --}}
    <div class="card" style="border-color:#fecaca;">
        <div class="card-header"><h2 style="color:#b91c1c;">Zona de peligro</h2></div>
        <div class="card-body">
            <p style="font-size:13px; color:var(--text-3); margin-bottom:16px; line-height:1.6;">
                Eliminar tu cuenta es permanente. Tus reservas pasadas quedan en el historial del estudio, pero perderás acceso a tu perfil, galerías y pagos pendientes.
            </p>
            <button type="button" class="btn btn-outline" style="border-color:#dc2626; color:#dc2626;"
                    onclick="document.getElementById('modalEliminarCuenta').classList.add('open')">
                Eliminar mi cuenta
            </button>
        </div>
    </div>

    {{-- Modal de confirmación --}}
    <div class="modal-gal" id="modalEliminarCuenta">
        <div class="modal-gal__sheet">
            <div class="modal-gal__pill"></div>
            <div class="modal-gal__titulo">¿Eliminar tu cuenta?</div>
            <p style="font-size:13px; color:var(--text-3); margin-bottom:20px;">Confirma tu contraseña para continuar. Esta acción no se puede deshacer.</p>

            <form method="POST" action="{{ route('profile.destroy') }}">
                @csrf @method('DELETE')
                <div class="form-group">
                    <label for="password-confirmar-borrado">Contraseña</label>
                    <input type="password" id="password-confirmar-borrado" name="password" required autocomplete="current-password">
                    @error('password', 'userDeletion')<span class="field-error">{{ $message }}</span>@enderror
                </div>
                <div class="form-actions" style="margin-top:16px;">
                    <button type="submit" class="btn" style="background:#dc2626; color:#fff; width:100%;">Sí, eliminar mi cuenta</button>
                    <button type="button" class="btn btn-outline" style="width:100%;" onclick="document.getElementById('modalEliminarCuenta').classList.remove('open')">Cancelar</button>
                </div>
            </form>
        </div>
    </div>

@endsection

{{-- ══════════════════════════════════════════
     ESTILOS ESPECÍFICOS DE PERFIL
══════════════════════════════════════════ --}}
@push('styles')
    <style>
        /* Botones del formulario: full-width apilados en móvil */
        .form-actions {
            display: flex;
            flex-direction: column;
            gap: 10px;
            margin-top: 8px;
        }
        .form-actions .btn { width: 100%; justify-content: center; }

        /* Mini-chart dentro de perfil-card — solo desktop */
        .perfil-card__chart { display: none; }
        .perfil-card__chart-label {
            font-size: 11px;
            color: var(--text-3);
            display: block;
            margin-bottom: 6px;
        }
        .chart-wrap--mini { height: 70px; }

        @media (min-width: 769px) {
            /* En desktop: botones en línea, ancho automático */
            .form-actions {
                flex-direction: row;
            }
            .form-actions .btn { width: auto; }

            /* Mostrar mini-chart dentro de la card de perfil */
            .perfil-card__chart {
                display: block;
                flex: 0 0 220px;
                margin-left: auto;
                align-self: stretch;
            }
        }

        .perfil-facts {
            display: flex;
            align-items: center;
            background: #fff;
            border: 1px solid var(--border);
            border-radius: 14px;
            padding: 18px 8px;
            margin-bottom: 20px;
        }
        .perfil-fact {
            flex: 1;
            text-align: center;
        }
        .perfil-fact__val {
            font-family: 'Playfair Display', serif;
            font-size: 26px;
            font-weight: 600;
            color: var(--navy, #1a2332);
            line-height: 1.1;
        }
        .perfil-fact__label {
            font-size: 11px;
            color: var(--text-3, #8a8478);
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .perfil-fact-divider {
            width: 1px;
            height: 32px;
            background: var(--border);
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
    <script>
        // Si venimos de un error o éxito relacionado a la contraseña,
        // llevamos al usuario directo a esa card en vez de dejarlo arriba del todo.
        @if ($errors->updatePassword->any() || session('status') === 'password-updated')
        document.getElementById('cambiar-password')?.scrollIntoView({
            behavior: 'smooth',
            block: 'center'
        });
        @endif
    </script>
@endpush
