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

    <div class="reservas-wrapper">

        {{-- TABS --}}
        <div class="reservas-tabs">
            <button class="tab active" data-tab="calcular">Calcular Nómina</button>
            <button class="tab" data-tab="historial">Historial</button>
            <button class="tab" data-tab="disputas">Disputas</button>
        </div>

        {{-- PANEL: Calcular --}}
        <div class="tab-panel active" id="panel-calcular">
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

            {{-- Listado de fotógrafos activos del período --}}
            <div class="card" style="margin-top:20px;">
                <div class="card-header">
                    <h2>Fotógrafos con sesiones en {{ $meses[(int) request('mes', now()->month)] }} {{ request('anio', now()->year) }}</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $fotografos->count() }} fotógrafo(s) encontrado(s)</span>
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
                            <input type="hidden" name="mes" value="{{ request('mes', now()->month) }}">
                            <input type="hidden" name="anio" value="{{ request('anio', now()->year) }}">
                            <button type="submit" class="btn btn-primary btn-sm">
                                Calcular Nómina
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL: Historial --}}
        <div class="tab-panel" id="panel-historial">
            <div class="card">
                <div class="card-header">
                    <h2>Historial de Nóminas Procesadas</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $nominas->count() }} registro(s)</span>
                </div>

                @if($nominas->isEmpty())
                    <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                        Aún no se ha calculado ninguna nómina.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Período</th>
                                <th>Calculada por</th>
                                <th>Fotógrafos</th>
                                <th>Total Neto</th>
                                <th>Estado</th>
                                <th>Fecha de cálculo</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($nominas as $nomina)
                                <tr>
                                    <td>{{ $nomina->periodo }}</td>
                                    <td>
                                        {{ $nomina->creadaPor->empleado->usuario->persona->nombre ?? '—' }}
                                        {{ $nomina->creadaPor->empleado->usuario->persona->apellido ?? '' }}
                                    </td>
                                    <td>{{ $nomina->detalles->count() }}</td>
                                    <td>RD$ {{ number_format($nomina->total_nomina_neta, 2) }}</td>
                                    <td>
                                        @php
                                            $colores = [
                                                'PENDIENTE'  => 'badge-inactive',
                                                'CALCULADA'  => 'badge-foto',
                                                'CONFIRMADA' => 'badge-active',
                                                'PAGADA'     => 'badge-active',
                                                'CERRADA'    => 'badge-admin',
                                            ];
                                        @endphp
                                        <span class="badge {{ $colores[$nomina->estado] ?? 'badge-admin' }}">
                                                {{ $nomina->estado }}
                                            </span>
                                    </td>
                                    <td>{{ $nomina->created_at->format('d/m/Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('admin.nomina.resumen', $nomina) }}" class="btn btn-outline btn-sm">
                                            Ver detalle
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

        {{-- PANEL: Disputas --}}
        <div class="tab-panel" id="panel-disputas">
            <div class="card">
                <div class="card-header">
                    <h2>Disputas Pendientes de Resolución</h2>
                    <span style="font-size:12px; color:var(--muted);">{{ $disputas->count() }} reporte(s)</span>
                </div>

                @if($disputas->isEmpty())
                    <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                        No hay disputas pendientes.
                    </div>
                @else
                    <div class="table-wrap">
                        <table class="table">
                            <thead>
                            <tr>
                                <th>Fotógrafo</th>
                                <th>Período</th>
                                <th>Observación</th>
                                <th></th>
                            </tr>
                            </thead>
                            <tbody>
                            @foreach($disputas as $disputa)
                                @php $persona = $disputa->fotografo->empleado->usuario->persona; @endphp
                                <tr>
                                    <td>{{ $persona->nombre }} {{ $persona->apellido }}</td>
                                    <td>{{ $disputa->nomina->periodo }}</td>
                                    <td style="max-width:320px; white-space:nowrap; overflow:hidden; text-overflow:ellipsis;">
                                        {{ $disputa->observacion_fotografo }}
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.nomina.disputa.show', $disputa) }}" class="btn btn-primary btn-sm">
                                            Resolver
                                        </a>
                                    </td>
                                </tr>
                            @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>

    </div>
@endsection

@push('scripts')
    <script>
        document.querySelectorAll('.tab').forEach(tab => {
            tab.addEventListener('click', () => {
                document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
                document.querySelectorAll('.tab-panel').forEach(p => p.classList.remove('active'));
                tab.classList.add('active');
                document.getElementById('panel-' + tab.dataset.tab).classList.add('active');
            });
        });
    </script>
@endpush
