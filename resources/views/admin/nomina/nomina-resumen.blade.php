@php use App\Services\NominaService; @endphp
@php use App\Support\Dinero; @endphp
@extends('layouts.admin')
@section('title', 'Resumen de Nómina')

@section('topbar-actions')
    <a href="{{ route('admin.nomina.pdf', $nomina) }}" class="btn btn-outline btn-sm">📄 Exportar PDF</a>
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
                <tr><td>Total Bruto</td><td>{{ Dinero::formato($nomina->total_salarios_brutos) }}</td></tr>
                @if($nomina->total_regalia_pascual > 0)
                    <tr><td>Regalía Pascual</td><td>{{ Dinero::formato($nomina->total_regalia_pascual) }}</td></tr>
                @endif
                @if($nomina->total_incentivos > 0)
                    <tr><td>Incentivos por Ventas</td><td>{{ Dinero::formato($nomina->total_incentivos) }}</td></tr>
                @endif
                <tr><td><strong>Total Neto a Pagar</strong></td><td><strong>{{ Dinero::formato($nomina->total_nomina_neta) }}</strong></td></tr>
            </table>

            {{-- Retenciones a los empleados, a favor de la DGII/TSS --}}
            <div style="margin-top:20px; padding:16px; background:#f9fafb; border-radius:8px;">
                <strong style="font-size:13px; display:block; margin-bottom:10px;">Retenciones a empleados </strong>
                <table class="detail-table">
                    <tr>
                        <td>TSS (SFS + AFP empleado)</td>
                        <td>{{ Dinero::formato($nomina->detalles->sum('descuento_tss')) }}</td>
                    </tr>
                    <tr>
                        <td>Dependientes adicionales (TSS)</td>
                        <td>{{ Dinero::formato($nomina->detalles->sum('descuento_dependientes')) }}</td>
                    </tr>
                    <tr>
                        <td>ISR retenido (a favor de la DGII)</td>
                        <td>{{ Dinero::formato($nomina->total_isr_retenido) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total retenido a empleados</strong></td>
                        <td><strong>{{ Dinero::formato($nomina->total_descuentos_legales) }}</strong></td>
                    </tr>
                </table>
            </div>

            {{-- Lo que paga el estudio aparte, no se descuenta a nadie --}}
            <div style="margin-top:12px; padding:16px; background:#f9fafb; border-radius:8px;">
                <strong style="font-size:13px; display:block; margin-bottom:10px;">Aportes patronales </strong>
                <table class="detail-table">
                    <tr>
                        <td>SFS + AFP + Riesgo Laboral </td>
                        <td>{{ Dinero::formato($nomina->total_aportes_patronales) }}</td>
                    </tr>
                </table>
            </div>

            @if($nomina->total_regalia_pascual > 0)
                {{-- Regalía Pascual: pago especial de diciembre, no lleva descuentos --}}
                <div style="margin-top:12px; padding:16px; background:#f9fafb; border-radius:8px;">
                    <strong style="font-size:13px; display:block; margin-bottom:10px;">Regalía Pascual </strong>
                    <table class="detail-table">
                        <tr>
                            <td>Total regalía pagada (sin descuentos de TSS/ISR)</td>
                            <td>{{ Dinero::formato($nomina->total_regalia_pascual) }}</td>
                        </tr>
                    </table>
                </div>
            @endif

            @if($nomina->total_incentivos > 0)
                {{-- Incentivo por ventas: sí lleva TSS/ISR (ya incluido en el bruto de cada detalle) --}}
                <div style="margin-top:12px; padding:16px; background:#fff3e8; border-radius:8px;">
                    <strong style="font-size:13px; display:block; margin-bottom:10px;">Incentivos por Ventas </strong>
                    <table class="detail-table">
                        <tr>
                            <td>Total incentivos pagados (incluido en el bruto, con descuentos aplicados)</td>
                            <td>{{ Dinero::formato($nomina->total_incentivos) }}</td>
                        </tr>
                    </table>
                </div>
            @endif
        </div>

        {{-- Confirmación del admin, condicionada a que todos los fotógrafos confirmaron --}}
        @if($nomina->estado === 'CALCULADA')
            <div style="padding:16px 20px; border-top:1px solid var(--border); text-align:right;">
                @if($nomina->todosFotografosConfirmaron())
                    <form method="POST" action="{{ route('admin.nomina.confirmar', $nomina) }}"
                          onsubmit="return confirm('¿Confirmar esta nómina?')">
                        @csrf
                        <button type="submit" class="btn btn-primary">
                            Confirmar Nómina
                        </button>
                    </form>
                @else
                    @php
                        $pendientes = $nomina->detallesPendientesDeRevision();
                    @endphp
                    <div
                        style="padding:10px 16px; background:#fef3c7; color:#92400e; border-radius:8px; font-size:13px; display:inline-block;">
                        Esperando confirmación de {{ $pendientes }} fotógrafo(s) antes de poder cerrar esta nómina.
                    </div>
                @endif
            </div>
        @elseif($nomina->estado === 'CONFIRMADA')
            <div style="padding:16px 20px; border-top:1px solid var(--border); background:#d1fae5; color:#065f46; display:flex; justify-content:space-between; align-items:center;">
                <span>✓ Todos los fotógrafos confirmaron.</span>
                <button type="button" class="btn btn-primary" data-modal="marcar-pagada">
                    Marcar como pagada
                </button>
            </div>
        @elseif($nomina->estado === 'CERRADA')
            <div style="padding:16px 20px; border-top:1px solid var(--border); background:#e5e7eb; color:#374151;">
                Nómina pagada y cerrada. Este proceso ha finalizado.
            </div>
        @endif
    </div>

    @foreach($desglose as $detalle)
        @php $persona = $detalle->fotografo->empleado->usuario->persona; @endphp
        <div class="card" style="margin-top:20px;">
            <div class="card-header">
                <h2>{{ $persona->nombre }} {{ $persona->apellido }}</h2>
                <span style="font-size:13px; color:var(--muted);">
                    Neto: {{ Dinero::formato($detalle->sueldo_neto) }}
                    @if($detalle->estaConfirmado())
                        <span class="badge badge-active" style="margin-left:6px;">Confirmado</span>
                    @elseif($detalle->estaEnDisputa())
                        <span class="badge"
                              style="background:#fef3c7; color:#92400e; margin-left:6px;">En disputa</span>
                    @else
                        <span class="badge badge-inactive" style="margin-left:6px;">Pendiente</span>
                    @endif
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
                    @foreach($detalle->participaciones_detalle as $p)
                        @php
                            $tasa = NominaService::tasaComision($p);
                            $monto = NominaService::montoComision($p);
                        @endphp
                        <tr>
                            <td>{{ $p->sesion->fecha_inicio->format('d/m/Y') }}</td>
                            <td><span
                                    class="badge {{ $p->rol === 'PRINCIPAL' ? 'badge-foto' : 'badge-admin' }}">{{ $p->rol }}</span>
                            </td>
                            <td>{{ Dinero::formato($p->sesion->reserva->precio_total) }}</td>
                            <td>{{ $tasa }}%</td>
                            <td>{{ Dinero::formato($monto) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; font-size:13px; flex-wrap:wrap; gap:8px;">
                <span>Salario Base: <strong>{{ Dinero::formato($detalle->fotografo->salarioBaseEfectivo()) }}</strong></span>
                <span>Bruto: <strong>{{ Dinero::formato($detalle->salario_bruto) }}</strong></span>
                <span>TSS: <strong>{{ Dinero::formato($detalle->descuento_tss) }}</strong></span>
                @if($detalle->dependientes_adicionales_aplicados > 0)
                    <span>Dependientes ({{ $detalle->dependientes_adicionales_aplicados }}): <strong>{{ Dinero::formato($detalle->descuento_dependientes) }}</strong></span>
                @endif
                <span>ISR: <strong>{{ Dinero::formato($detalle->descuento_isr) }}</strong></span>
                @if($detalle->regalia_pascual > 0)
                    <span>Regalía Pascual: <strong>{{ Dinero::formato($detalle->regalia_pascual) }}</strong></span>
                @endif
                @if($detalle->incentivo_ventas > 0)
                    <span>Incentivo: <strong>{{ Dinero::formato($detalle->incentivo_ventas) }}</strong></span>
                @endif
                <span>Neto: <strong>{{ Dinero::formato($detalle->sueldo_neto) }}</strong></span>
            </div>
        </div>
    @endforeach

    {{-- Modal: confirmar marcar como pagada --}}
    <div id="modal-marcar-pagada" class="mp-overlay">
        <div class="mp-panel">
            <h3 class="mp-titulo">Marcar nómina como pagada</h3>
            <p class="mp-texto">
                ¿Ya realizaste el pago a todos los fotógrafos de este período?
                Esto cerrará el proceso de esta nómina de forma definitiva.
            </p>

            <form method="POST" action="{{ route('admin.nomina.pagar', $nomina) }}" class="mp-acciones"
                  onsubmit="return confirm('Esto cerrará el proceso de esta nómina de forma definitiva. ¿Continuar?')">
                @csrf
                <button type="submit" class="btn btn-primary">Marcar como pagada y cerrar</button>
            </form>

            <button type="button" class="mp-cancelar">Cancelar</button>
        </div>
    </div>

    <script>
        (function () {
            const modal = document.getElementById('modal-marcar-pagada');
            if (!modal) return;

            document.addEventListener('click', e => {
                if (e.target.closest('[data-modal="marcar-pagada"]')) {
                    modal.classList.add('open');
                    document.body.style.overflow = 'hidden';
                }
            });

            modal.querySelector('.mp-cancelar').addEventListener('click', () => {
                modal.classList.remove('open');
                document.body.style.overflow = '';
            });

            modal.addEventListener('click', e => {
                if (e.target === modal) {
                    modal.classList.remove('open');
                    document.body.style.overflow = '';
                }
            });
        })();
    </script>
@endsection
