@extends('layouts.admin')
@section('title', 'Nómina')

@section('content')
    @if(session('nomina_calculada'))
        <div class="alert alert-success" style="margin-bottom:16px; padding:12px 16px; background:#d1fae5; color:#065f46; border-radius:8px; display:flex; justify-content:space-between; align-items:center;">
            <span>¡Nómina calculada correctamente!</span>
            <a href="{{ route('admin.nomina.resumen', session('nomina_calculada')) }}" class="btn btn-primary btn-sm">
                Ver resumen
            </a>
        </div>
    @endif
    @if(session('periodo_ya_procesado'))
        <div class="alert" style="margin-bottom:16px; padding:12px 16px; background:#fef3c7; color:#92400e; border-radius:8px;">
            <p style="margin:0 0 10px;">
                ⚠️ El período <strong>{{ session('periodo_ya_procesado') }}</strong> ya fue procesado anteriormente.
                ¿Deseas recalcularlo? Esto generará una nueva nómina para el mismo período.
            </p>
            <form method="POST" action="{{ route('admin.nomina.calcular') }}">
                @csrf
                <input type="hidden" name="mes" value="{{ request('mes') }}">
                <input type="hidden" name="anio" value="{{ request('anio') }}">
                <input type="hidden" name="confirmar_recalculo" value="1">
                <button type="submit" class="btn btn-primary btn-sm">
                    Sí, recalcular
                </button>
            </form>
        </div>
    @endif

    <div class="card">
        <div class="card-header">
            <h2>Selecciona el período</h2>
            <span style="font-size:12px; color:var(--muted);">Elige mes y año para calcular la nómina</span>
        </div>

        <form method="GET" action="{{ route('admin.nomina') }}" style="padding:20px; display:flex; gap:16px; align-items:end; flex-wrap:wrap;">
            <div>
                <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Mes</label>
                <select name="mes" onchange="this.form.submit()" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:160px;">
                    @foreach($meses as $num => $nombre)
                        <option value="{{ $num }}" {{ (int) request('mes', now()->month) === $num ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Año</label>
                <select name="anio" onchange="this.form.submit()" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:120px;">
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}" {{ (int) request('anio', now()->year) === $anio ? 'selected' : '' }}>
                            {{ $anio }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>

    {{--  Listado de fotógrafos activos del período --}}
    @if($fotografos !== null)
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <h2>Fotógrafos con sesiones en {{ $meses[(int) request('mes', now()->month)] }} {{ request('anio', now()->year) }}</h2>                <span style="font-size:12px; color:var(--muted);">{{ $fotografos->count() }} fotógrafo(s) encontrado(s)</span>
            </div>

            @if($fotografos->isEmpty())
                <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                    No hay sesiones finalizadas en este período.
                </div>
            @else
                <div class="table-wrap">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Fotógrafo</th>
                            <th>Sesiones como Principal</th>
                            <th>Sesiones como Asistente</th>
                            <th>Total sesiones</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($fotografos as $fotografo)
                            <tr>
                                <td>
                                    {{ $fotografo->empleado->usuario->persona->nombre ?? '' }}
                                    {{ $fotografo->empleado->usuario->persona->apellido ?? '' }}
                                </td>
                                <td>{{ $fotografo->sesiones_principal }}</td>
                                <td>{{ $fotografo->sesiones_asistente }}</td>
                                <td><strong>{{ $fotografo->total_sesiones }}</strong></td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

                <div style="padding:16px 20px; text-align:right;">
                    <form method="POST" action="{{ route('admin.nomina.calcular') }}">
                        @csrf
                        <input type="hidden" name="mes" value="{{ request('mes') }}">
                        <input type="hidden" name="anio" value="{{ request('anio') }}">
                        <button type="submit" class="btn btn-primary btn-sm">
                            Calcular Nómina
                        </button>
                    </form>
                </div>
            @endif
        </div>
    @endif
@endsection
