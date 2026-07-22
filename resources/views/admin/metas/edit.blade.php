@extends('layouts.admin')
@section('title', 'Configurar Metas')

@section('content')

    <div class="panel" style="max-width:520px">
        <div class="panel-header">
            <div>
                <div class="panel-title">Meta de {{ \Carbon\Carbon::create($anio, $mes)->translatedFormat('F Y') }}</div>
            </div>
        </div>

        <div class="panel-body">

            @if(session('success'))
                <div class="perf-pill" style="margin-bottom:18px">{{ session('success') }}</div>
            @endif

            <form method="POST" action="{{ route('admin.metas.actualizar') }}">
                @csrf
                <input type="hidden" name="mes" value="{{ $mes }}">
                <input type="hidden" name="anio" value="{{ $anio }}">

                <div class="form-group">
                    <label for="ingresos">Meta de Ingresos ($)</label>
                    <input type="number" step="0.01" min="0" id="ingresos" name="ingresos"
                           value="{{ old('ingresos', $meta->ingresos) }}" required>
                    @error('ingresos')<span class="field-error">{{ $message }}</span>@enderror
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="reservas">Meta de Reservas</label>
                        <input type="number" min="0" id="reservas" name="reservas"
                               value="{{ old('reservas', $meta->reservas) }}" required>
                        @error('reservas')<span class="field-error">{{ $message }}</span>@enderror
                    </div>

                    <div class="form-group">
                        <label for="clientes_nuevos">Meta de Clientes Nuevos</label>
                        <input type="number" min="0" id="clientes_nuevos" name="clientes_nuevos"
                               value="{{ old('clientes_nuevos', $meta->clientes_nuevos) }}" required>
                        @error('clientes_nuevos')<span class="field-error">{{ $message }}</span>@enderror
                    </div>
                </div>

                <div class="form-actions">
                    <button type="submit" class="btn btn-primary">Guardar Meta</button>
                    <a href="{{ route('admin.dashboard') }}" class="btn btn-outline">Cancelar</a>
                </div>
            </form>

        </div>
    </div>

@endsection
