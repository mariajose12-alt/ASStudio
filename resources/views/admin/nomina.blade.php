@extends('layouts.admin')
@section('title', 'Nómina')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Selecciona el período</h2>
            <span style="font-size:12px; color:var(--muted);">Elige mes y año para calcular la nómina</span>
        </div>

        <form method="GET" action="{{ route('admin.nomina') }}" style="padding:20px; display:flex; gap:16px; align-items:end; flex-wrap:wrap;">
            <div>
                <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Mes</label>
                <select name="mes" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:160px;">
                    @foreach($meses as $num => $nombre)
                        <option value="{{ $num }}" {{ (int) request('mes', now()->month) === $num ? 'selected' : '' }}>
                            {{ $nombre }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">Año</label>
                <select name="anio" style="padding:8px 12px; border-radius:8px; border:1px solid var(--border); min-width:120px;">
                    @foreach($anios as $anio)
                        <option value="{{ $anio }}" {{ (int) request('anio', now()->year) === $anio ? 'selected' : '' }}>
                            {{ $anio }}
                        </option>
                    @endforeach
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm" style="height:38px;">
                Buscar fotógrafos
            </button>
        </form>
    </div>
@endsection
