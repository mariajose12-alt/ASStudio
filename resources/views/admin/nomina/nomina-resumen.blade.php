@php use App\Services\NominaService; @endphp
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
                <tr><td>Total Bruto</td><td>RD$ {{ number_format($nomina->total_salarios_brutos, 2) }}</td></tr>
                <tr><td><strong>Total Neto a Pagar</strong></td><td><strong>RD$ {{ number_format($nomina->total_nomina_neta, 2) }}</strong></td></tr>
            </table>

            {{-- Retenciones a los empleados, a favor de la DGII/TSS --}}
            <div style="margin-top:20px; padding:16px; background:#f9fafb; border-radius:8px;">
                <strong style="font-size:13px; display:block; margin-bottom:10px;">Retenciones a empleados </strong>
                <table class="detail-table">
                    <tr>
                        <td>TSS (SFS + AFP empleado)</td>
                        <td>RD$ {{ number_format($nomina->detalles->sum('descuento_tss'), 2) }}</td>
                    </tr>
                    <tr>
                        <td>Dependientes adicionales (TSS)</td>
                        <td>RD$ {{ number_format($nomina->detalles->sum('descuento_dependientes'), 2) }}</td>
                    </tr>
                    <tr>
                        <td>ISR retenido (a favor de la DGII)</td>
                        <td>RD$ {{ number_format($nomina->total_isr_retenido, 2) }}</td>
                    </tr>
                    <tr>
                        <td><strong>Total retenido a empleados</strong></td>
                        <td><strong>RD$ {{ number_format($nomina->total_descuentos_legales, 2) }}</strong></td>
                    </tr>
                </table>
            </div>

            {{-- Lo que paga el estudio aparte, no se descuenta a nadie --}}
            <div style="margin-top:12px; padding:16px; background:#f9fafb; border-radius:8px;">
                <strong style="font-size:13px; display:block; margin-bottom:10px;">Aportes patronales </strong>
                <table class="detail-table">
                    <tr>
                        <td>SFS + AFP + Riesgo Laboral (patronal)</td>
                        <td>RD$ {{ number_format($nomina->total_aportes_patronales, 2) }}</td>
                    </tr>
                </table>
            </div>
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
        @elseif($nomina->estado === 'PAGADA')
            <div style="padding:16px 20px; border-top:1px solid var(--border); background:#dbeafe; color:#1e40af;">
                 Nómina pagada. Pendiente de cierre.
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
                    Neto: RD$ {{ number_format($detalle->sueldo_neto, 2) }}
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
                            <td>RD$ {{ number_format($p->sesion->reserva->precio_total, 2) }}</td>
                            <td>{{ $tasa }}%</td>
                            <td>RD$ {{ number_format($monto, 2) }}</td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div style="padding:16px 20px; border-top:1px solid var(--border); display:flex; justify-content:space-between; font-size:13px; flex-wrap:wrap; gap:8px;">
                <span>Salario Base: <strong>RD$ {{ number_format($detalle->fotografo->salarioBaseEfectivo(), 2) }}</strong></span>
                <span>Bruto: <strong>RD$ {{ number_format($detalle->salario_bruto, 2) }}</strong></span>
                <span>TSS: <strong>RD$ {{ number_format($detalle->descuento_tss, 2) }}</strong></span>
                @if($detalle->dependientes_adicionales_aplicados > 0)
                    <span>Dependientes ({{ $detalle->dependientes_adicionales_aplicados }}): <strong>RD$ {{ number_format($detalle->descuento_dependientes, 2) }}</strong></span>
                @endif
                <span>ISR: <strong>RD$ {{ number_format($detalle->descuento_isr, 2) }}</strong></span>
                <span>Neto: <strong>RD$ {{ number_format($detalle->sueldo_neto, 2) }}</strong></span>
            </div>
        </div>
    @endforeach

    {{-- Modal: confirmar marcar como pagada --}}
    <div id="modal-marcar-pagada" class="mp-overlay">
        <div class="mp-panel">
            <h3 class="mp-titulo">Marcar nómina como pagada</h3>
            <p class="mp-texto">
                ¿Ya realizaste el pago a todos los fotógrafos de este período?
                Puedes marcarla solo como pagada, o pagarla y cerrar el proceso
                definitivamente.
            </p>

            <form method="POST" action="{{ route('admin.nomina.pagar', $nomina) }}" class="mp-acciones">
                @csrf
                <input type="hidden" name="cerrar" value="0">
                <button type="submit" class="btn btn-outline">Solo marcar como pagada</button>
            </form>

            <form method="POST" action="{{ route('admin.nomina.pagar', $nomina) }}" class="mp-acciones"
                  onsubmit="return confirm('Esto cerrará el proceso de esta nómina de forma definitiva. ¿Continuar?')">
                @csrf
                <input type="hidden" name="cerrar" value="1">
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
