@php use App\Services\NominaService; @endphp
@extends('layouts.fotografo')
@section('title', 'Detalle de Nómina')

@section('content')
    <div style="margin-bottom:16px;">
        <a href="{{ route('fotografo.nomina.index') }}" class="btn btn-outline btn-sm">← Volver</a>
    </div>

    <div class="card">
        <div class="card-header">
            <h2>Nómina — {{ $detalle->nomina->periodo }}</h2>
            <span style="font-size:13px; color:var(--muted);">
                {{ $detalle->nomina->fecha_inicio->format('d/m/Y') }} — {{ $detalle->nomina->fecha_fin->format('d/m/Y') }}
            </span>
        </div>

        <div class="card-body">
            <table class="detail-table">
                <tr>
                    <td>Salario Bruto</td>
                    <td>RD$ {{ number_format($detalle->salario_bruto, 2) }}</td>
                </tr>
                <tr>
                    <td>Descuentos Legales</td>
                    <td>RD$ {{ number_format($detalle->descuentos_legales, 2) }}</td>
                </tr>
                <tr>
                    <td><strong>Neto a Recibir</strong></td>
                    <td><strong>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</strong></td>
                </tr>
            </table>
        </div>

        {{-- Estado y acciones del fotógrafo --}}
        <div style="padding:16px 20px; border-top:1px solid var(--border);">
            @if($detalle->estaPendiente())
                <div style="margin-bottom:14px;">
                    <span class="badge badge-inactive">Pendiente de revisión</span>
                    <p style="margin:8px 0 0; font-size:13px; color:var(--muted);">
                        Revisa el desglose y confirma si está correcto, o reporta un ajuste si encuentras algún error.
                    </p>
                </div>

                <div style="display:flex; gap:10px; flex-wrap:wrap;">
                    <form method="POST" action="{{ route('fotografo.nomina.confirmar', $detalle) }}"
                          onsubmit="return confirm('¿Confirmas que este desglose es correcto y apruebas que se proceda con tu pago?')">
                        @csrf
                        <button type="submit" class="btn btn-primary btn-sm">Confirmar desglose</button>
                    </form>

                    <button type="button" class="btn btn-outline btn-sm"
                            onclick="document.getElementById('form-ajuste').style.display='block'">
                        Reportar ajuste
                    </button>
                </div>

                <form id="form-ajuste" method="POST" action="{{ route('fotografo.nomina.ajuste', $detalle) }}"
                      style="display:none; margin-top:14px;">
                    @csrf
                    <label style="display:block; font-size:13px; color:var(--muted); margin-bottom:6px;">
                        Explica qué ajuste necesitas (mínimo 10 caracteres):
                    </label>
                    <textarea name="observacion_fotografo" rows="3" maxlength="255"
                              style="width:100%; padding:10px; border-radius:8px; border:1px solid var(--border); font-size:13px;"
                              placeholder="Ej: La sesión del 15/06 no aparece en mi desglose, pero sí la trabajé como asistente.">{{ old('observacion_fotografo') }}</textarea>
                    @error('observacion_fotografo')
                    <p style="color:#dc2626; font-size:12px; margin-top:4px;">{{ $message }}</p>
                    @enderror
                    <button type="submit" class="btn btn-primary btn-sm" style="margin-top:10px;"
                            onclick="return confirm('¿Enviar este reporte de ajuste al administrador?')">
                        Enviar reporte
                    </button>
                </form>

            @elseif($detalle->estaConfirmado())
                <span class="badge badge-active">Confirmado</span>
                <p style="margin:8px 0 0; font-size:13px; color:var(--muted);">
                    Ya confirmaste este desglose
                    @if($detalle->confirmado_at)
                        el {{ $detalle->confirmado_at->format('d/m/Y H:i') }}
                    @endif
                    . Tu pago será procesado según este detalle.
                </p>
            @elseif($detalle->estaEnDisputa())
                <span class="badge" style="background:#fef3c7; color:#92400e;">En disputa</span>
                <p style="margin:8px 0 0; font-size:13px; color:var(--muted);">
                    Reportaste el siguiente ajuste y está en espera de revisión por el administrador:
                </p>
                <div
                    style="margin-top:8px; padding:10px 14px; background:#fef3c7; color:#92400e; border-radius:8px; font-size:13px;">
                    {{ $detalle->observacion_fotografo }}
                </div>
            @endif
        </div>
    </div>

    <div class="card" style="margin-top:20px;">
        <div class="card-header">
            <h2>Desglose por sesión</h2>
            <span
                style="font-size:12px; color:var(--muted);">{{ $participaciones->count() }} sesión(es) en este período</span>
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
                @foreach($participaciones as $p)
                    @php
                        $tasa = NominaService::tasaComision($p);
                        $monto = NominaService::montoComision($p);
                    @endphp
                    <tr>
                        <td>{{ $p->sesion->fecha_inicio->format('d/m/Y') }}</td>
                        <td><span
                                class="badge {{ $p->rol === 'PRINCIPAL' ? 'badge-foto' : 'badge-admin' }}">{{ $p->rol }}</span>
                        </td>
                        <td>RD$ {{ number_format($p->sesion->reserva->precio_total, 2) }}</td>
                        <td>{{ $tasa }}%</td>
                        <td>RD$ {{ number_format($monto, 2) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
@endsection
