@extends('layouts.fotografo')
@section('title', 'Mi Nómina')

@section('content')
    <div class="card">
        <div class="card-header">
            <h2>Mis Reportes Pendientes de Confirmación</h2>
        </div>

        @if($detalles->isEmpty())
            <div style="padding:32px; text-align:center; color:var(--muted); font-size:14px;">
                Aún no tienes nóminas calculadas.
            </div>
        @else
            <div class="table-wrap">
                <table class="table">
                    <thead>
                    <tr>
                        <th>Período</th>
                        <th>Bruto</th>
                        <th>Descuentos</th>
                        <th>Neto a Recibir</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    @foreach($detalles as $detalle)
                        <tr>
                            <td>{{ $detalle->nomina->periodo }}</td>
                            <td>RD$ {{ number_format($detalle->salario_bruto, 2) }}</td>
                            <td>RD$ {{ number_format($detalle->descuentos_legales, 2) }}</td>
                            <td><strong>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</strong></td>
                            <td>
                                @if($detalle->estaConfirmado())
                                    <span class="badge badge-active">Confirmado</span>
                                @elseif($detalle->estaEnDisputa())
                                    <span class="badge" style="background:#fef3c7; color:#92400e;">En disputa</span>
                                @else
                                    <span class="badge badge-inactive">Pendiente de revisión</span>
                                @endif
                            </td>
                            <td>
                                <a href="{{ route('fotografo.nomina.show', $detalle) }}" class="btn btn-outline btn-sm">Ver detalle</a>
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
@endsection
