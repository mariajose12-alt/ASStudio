@extends('layouts.admin')
@section('title', 'Resumen de Nómina')

@section('topbar-actions')
    <a href="{{ route('admin.nomina') }}" class="btn btn-outline btn-sm">← Volver</a>
@endsection

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Nómina — {{ $nomina->periodo }}</h2>
            <span class="badge badge-foto">{{ $nomina->estado }}</span>
        </div>
        <div class="card-body">
            <table class="detail-table">
                <tr><td>Período</td><td>{{ $nomina->fecha_inicio->format('d/m/Y') }} — {{ $nomina->fecha_fin->format('d/m/Y') }}</td></tr>
                <tr><td>Creada por</td><td>{{ $nomina->creadaPor->empleado->usuario->persona->nombre ?? '—' }} {{ $nomina->creadaPor->empleado->usuario->persona->apellido ?? '' }}</td></tr>
                <tr><td>Total Bruto</td><td>RD$ {{ number_format($nomina->total_salarios_brutos, 2) }}</td></tr>
                <tr><td>Total Descuentos</td><td>RD$ {{ number_format($nomina->total_descuentos_legales, 2) }}</td></tr>
                <tr><td>Total Aportes Patronales</td><td>RD$ {{ number_format($nomina->total_aportes_patronales, 2) }}</td></tr>
                <tr><td><strong>Total Neto a Pagar</strong></td><td><strong>RD$ {{ number_format($nomina->total_nomina_neta, 2) }}</strong></td></tr>
            </table>
        </div>
    </div>

    @foreach($desglose as $detalle)
        @php $persona = $detalle->fotografo->empleado->usuario->persona; @endphp
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <h2>{{ $persona->nombre }} {{ $persona->apellido }}</h2>
                <span style="font-size:13px; color:var(--muted);">Neto: RD$ {{ number_format($detalle->sueldo_neto, 2) }}</span>
            </div>

            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Fecha Sesión</th>
                        <th>Rol</th>
                        <th>Precio Reserva</th>
                        <th>% Comisión</th>
                        <th>Monto Comisión</th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($detalle->participaciones_detalle as $p)
                        @php
                            $tasa = $p->porcentaje_comision > 0
                                ? $p->porcentaje_comision
                                : ($p->rol === 'PRINCIPAL' ? 40 : 20);
                            $monto = $p->sesion->reserva->precio_total * ($tasa / 100);
                        @endphp
                        <tr>
                            <td>{{ $p->sesion->fecha_inicio->format('d/m/Y') }}</td>
                            <td><span class="badge {{ $p->rol === 'PRINCIPAL' ? 'badge-foto' : 'badge-admin' }}">{{ $p->rol }}</span></td>
                            <td>RD$ {{ number_format($p->sesion->reserva->precio_total, 2) }}</td>
                            <td>{{ $tasa }}%</td>
                            <td>RD$ {{ number_format($monto, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; font-size:13px;">
                <span>Salario Base: <strong>RD$ {{ number_format($detalle->fotografo->salarioBaseEfectivo(), 2) }}</strong></span>
                <span>Bruto: <strong>RD$ {{ number_format($detalle->salario_bruto, 2) }}</strong></span>
                <span>Descuentos: <strong>RD$ {{ number_format($detalle->descuentos_legales, 2) }}</strong></span>
                <span>Neto: <strong>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</strong></span>
            </div>
        </div>
    @endforeach
@endsection
