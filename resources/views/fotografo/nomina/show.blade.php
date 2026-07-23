@php use App\Services\NominaService; @endphp
@php use App\Support\Dinero; @endphp
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
                    <td>{{ Dinero::formato($detalle->salario_bruto) }}</td>
                </tr>
                <tr>
                    <td>TSS (SFS + AFP)</td>
                    <td>{{ Dinero::formato($detalle->descuento_tss) }}</td>
                </tr>
                @if($detalle->dependientes_adicionales_aplicados > 0)
                    <tr>
                        <td>Dependientes adicionales TSS ({{ $detalle->dependientes_adicionales_aplicados }})</td>
                        <td>{{ Dinero::formato($detalle->descuento_dependientes) }}</td>
                    </tr>
                @endif
                <tr>
                    <td>ISR retenido</td>
                    <td>{{ Dinero::formato($detalle->descuento_isr) }}</td>
                </tr>
                <tr>
                    <td>Total Descuentos</td>
                    <td>{{ Dinero::formato($detalle->descuentos_legales) }}</td>
                </tr>
                @if($detalle->regalia_pascual > 0)
                    <tr>
                        <td>Regalía Pascual</td>
                        <td>{{ Dinero::formato($detalle->regalia_pascual) }}</td>
                    </tr>
                @endif
                @if($detalle->incentivo_ventas > 0)
                    <tr>
                        <td>Incentivo por Ventas</td>
                        <td>{{ Dinero::formato($detalle->incentivo_ventas) }}</td>
                    </tr>
                @endif
                <tr>
                    <td><strong>Neto a Recibir</strong></td>
                    <td><strong>{{ Dinero::formato($detalle->sueldo_neto) }}</strong></td>
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
                <div style="margin-top:8px; padding:10px 14px; background:#fef3c7; color:#92400e; border-radius:8px; font-size:13px;">
                    {{ $detalle->observacion_fotografo }}
                </div>
            @endif
        </div>
    </div>

    @if($participaciones->isNotEmpty())
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <h2>Desglose por sesión</h2>
                <span style="font-size:12px; color:var(--muted);">
                    {{ $participaciones->count() }} sesión(es) en este período
                </span>
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
                            <td>
                                <span class="badge {{ $p->rol === 'PRINCIPAL' ? 'badge-foto' : 'badge-admin' }}">
                                    {{ $p->rol }}
                                </span>
                            </td>
                            <td>{{ Dinero::formato($p->sesion->reserva->precio_total) }}</td>
                            <td>{{ $tasa }}%</td>
                            <td>{{ Dinero::formato($monto) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @else
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <h2>Desglose por sesión</h2>
            </div>

            <div class="card-body" style="text-align:center; padding:32px;">
                <p style="margin:0; color:var(--muted);">
                    No tuviste participaciones en sesiones durante este período de nómina.
                </p>
            </div>
        </div>
    @endif

@endsection
