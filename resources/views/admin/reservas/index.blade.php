@extends('layouts.admin')
@section('title', 'Reservas')


@section('content')
    @if($reservas->isEmpty())
        <div class="card" style="text-align:center; padding:60px; color:var(--muted);">
            No hay reservas registradas.
        </div>
    @endif

    <div style="display:flex; flex-direction:column; gap:16px;">
        @foreach($reservas as $reserva)
            @php
                $badgeClass = match($reserva->estado) {
                    'APROBADA'    => 'badge-active',
                    'RECHAZADA', 'CANCELADA' => 'badge-inactive',
                    'PAGO_RECIBIDO' => 'badge-admin',
                    default       => 'badge-foto',
                };
            @endphp

            <div class="card" style="border-radius:14px; overflow:visible;">
                <div class="card-body" style="padding:28px;">

                    {{-- Header de la card --}}
                    <div style="display:flex; justify-content:space-between; align-items:flex-start; margin-bottom:20px;">
                        <div>
                            <h3 style="font-family:'Playfair Display',serif; font-size:18px; font-weight:400; margin-bottom:4px;">
                                {{ $reserva->paquete->nombre ?? 'Paquete' }} · {{ $reserva->tipo }}
                            </h3>
                            <span style="font-size:12px; color:var(--muted);">#ID-{{ $reserva->id }}</span>
                        </div>
                        <span class="badge {{ $badgeClass }}" style="font-size:11px; padding:5px 14px;">
                            {{ $reserva->estado }}
                        </span>
                    </div>

                    <hr style="border:none; border-top:1px solid var(--border); margin-bottom:20px;">

                    {{-- Datos principales --}}
                    <div style="display:grid; grid-template-columns:repeat(4,1fr); gap:16px; margin-bottom:20px;">
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Fecha</div>
                            <div style="font-size:14px;">{{ $reserva->fecha_inicio->format('d \d\e F, Y') }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Horario</div>
                            <div style="font-size:14px;">{{ $reserva->fecha_inicio->format('H:i') }} hrs</div>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Ubicación</div>
                            <div style="font-size:14px;">{{ $reserva->lugar ?? 'Estudio' }}</div>
                        </div>
                        <div>
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:4px;">Precio</div>
                            <div style="font-size:14px; color:var(--navy);">RD$ {{ number_format($reserva->precio_total, 2) }}</div>
                        </div>
                    </div>

                    {{-- Descripción --}}
                    @if($reserva->descripcion)
                        <div style="background:var(--snow); border-radius:10px; padding:16px; margin-bottom:20px;">
                            <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:8px;">Descripción de la sesión</div>
                            <p style="font-size:13px; color:#555; margin:0; line-height:1.6;">{{ $reserva->descripcion }}</p>
                        </div>
                    @endif

                    {{-- Info del cliente --}}
                    <div style="margin-bottom:20px;">
                        <div style="font-size:10px; font-weight:600; letter-spacing:1.5px; text-transform:uppercase; color:var(--muted); margin-bottom:12px;">Información del Cliente</div>
                        <div style="display:grid; grid-template-columns:repeat(3,1fr); gap:16px;">
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Nombre y Apellido</div>
                                <div style="font-size:14px; margin-top:2px;">
                                    {{ $reserva->cliente->usuario->persona->nombre ?? '—' }} {{ $reserva->cliente->usuario->persona->apellido ?? '' }}
                                </div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Correo</div>
                                <div style="font-size:14px; margin-top:2px;">{{ $reserva->cliente->usuario->email ?? '—' }}</div>
                            </div>
                            <div>
                                <div style="font-size:12px; color:var(--muted);">Número Telefónico</div>
                                <div style="font-size:14px; margin-top:2px;">{{ $reserva->cliente->usuario->persona->telefono ?? '—' }}</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div style="padding:20px 0;">
        {{ $reservas->links() }}
    </div>

@endsection

