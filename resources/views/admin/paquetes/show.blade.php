@extends('layouts.admin')
@section('title', 'Detalle de Paquete')

@section('topbar-actions')
    <div style="display:flex; gap:8px;">
        <a href="{{ route('admin.paquetes.edit', $paquete) }}" class="btn btn-primary btn-sm">Editar</a>
        <a href="{{ route('admin.paquetes.index') }}" class="btn btn-outline btn-sm">← Volver</a>
    </div>
@endsection

@section('content')
    <div style="display:grid; grid-template-columns:1fr 320px; gap:24px;">

        {{-- Info principal --}}
        <div class="card">
            <div class="card-header">
                <h2>{{ $paquete->nombre }}</h2>
                <div style="display:flex; gap:8px; align-items:center;">
                    <span class="badge {{ $paquete->tipo === 'ESTUDIO' ? 'badge-foto' : 'badge-admin' }}">{{ $paquete->tipo }}</span>
                    <span class="badge {{ $paquete->activo ? 'badge-active' : 'badge-inactive' }}">{{ $paquete->activo ? 'Activo' : 'Inactivo' }}</span>
                </div>
            </div>
            <div class="card-body">
                @if($paquete->descripcion)
                    <p style="color:#555; font-size:14px; line-height:1.7; margin-bottom:24px;">{{ $paquete->descripcion }}</p>
                    <hr style="border:none; border-top:1px solid var(--border); margin-bottom:24px;">
                @endif

                <table class="detail-table">
                    <tr>
                        <td>ID</td>
                        <td>{{ $paquete->id }}</td>
                    </tr>
                    <tr>
                        <td>Tipo de sesión</td>
                        <td><span class="badge {{ $paquete->tipo === 'ESTUDIO' ? 'badge-foto' : 'badge-admin' }}">{{ $paquete->tipo }}</span></td>
                    </tr>
                    <tr>
                        <td>Fotos incluidas</td>
                        <td>{{ $paquete->cantidad_fotos_incluidas }} fotografías</td>
                    </tr>
                    <tr>
                        <td>Precio base</td>
                        <td>RD$ {{ number_format($paquete->precio_base, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Precio total</td>
                        <td style="font-weight:600; color:var(--gold); font-size:16px;">RD$ {{ number_format($paquete->precio_total, 2) }}</td>
                    </tr>
                    <tr>
                        <td>Estado</td>
                        <td><span class="badge {{ $paquete->activo ? 'badge-active' : 'badge-inactive' }}">{{ $paquete->activo ? 'Activo' : 'Inactivo' }}</span></td>
                    </tr>
                    <tr>
                        <td>Creado</td>
                        <td>{{ $paquete->created_at->format('d/m/Y H:i') }}</td>
                    </tr>
                    <tr>
                        <td>Actualizado</td>
                        <td>{{ $paquete->updated_at->format('d/m/Y H:i') }}</td>
                    </tr>
                </table>
            </div>
        </div>

        {{-- Panel lateral --}}
        <div style="display:flex; flex-direction:column; gap:16px;">

            {{-- Precio destacado --}}
            <div class="card">
                <div class="card-body" style="text-align:center; padding:32px 24px;">
                    <div style="font-size:11px; color:var(--muted); text-transform:uppercase; letter-spacing:1.5px; margin-bottom:8px;">Precio Total</div>
                    <div style="font-family:'Playfair Display', serif; font-size:42px; color:var(--gold); line-height:1;">
                        RD$ {{ number_format($paquete->precio_total, 2) }}
                    </div>
                    <div style="font-size:12px; color:var(--muted); margin-top:8px;">Base: RD$ {{ number_format($paquete->precio_base, 2) }}</div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="card">
                <div class="card-header"><h2>Acciones</h2></div>
                <div class="card-body" style="display:flex; flex-direction:column; gap:10px;">
                    <a href="{{ route('admin.paquetes.edit', $paquete) }}" class="btn btn-primary" style="justify-content:center;">
                        Editar paquete
                    </a>
                    <form method="POST" action="{{ route('admin.paquetes.destroy', $paquete) }}"
                          onsubmit="return confirm('¿Eliminar el paquete «{{ $paquete->nombre }}»? Esta acción no se puede deshacer.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger" style="width:100%; justify-content:center;">
                            Eliminar paquete
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
@endsection
