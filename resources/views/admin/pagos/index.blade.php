@extends('layouts.admin')
@section('title', 'Comprobantes Pendientes')

@section('content')
    @if($pagos->isEmpty())
        <div class="reserva-empty">
            No hay comprobantes pendientes.
        </div>
    @endif

    <div class="reserva-lista">
        @forelse($pagos as $pago)
            <div class="card reserva-card-f">
                <div class="card-body reserva-card-f-body">

                    <div class="reserva-header">
                        <div>
                            <h3 class="reserva-titulo">
                                {{ $pago->reserva->paquete->nombre ?? 'Paquete' }} · {{ $pago->tipo }}
                            </h3>
                            <span class="badge">{{ $pago->estado }}</span>
                        </div>
                    </div>

                    <hr class="reserva-divider">

                    <div class="reserva-grid-datos">
                        <div class="reserva-dato">
                            <span class="dato-label">Monto</span>
                            <span class="dato-valor dato-valor--precio">RD$ {{ number_format($pago->monto, 2) }}</span>
                        </div>
                        <div class="reserva-dato">
                            <span class="dato-label">Tipo</span>
                            <span class="dato-valor">{{ ucfirst(strtolower($pago->tipo)) }}</span>
                        </div>
                        <div class="reserva-dato">
                            <span class="dato-label">Subido</span>
                            <span class="dato-valor">{{ $pago->fecha_registro->format('d \d\e F, Y H:i') }}</span>
                        </div>
                        <div class="reserva-dato">
                            <span class="dato-label">Estado OCR</span>
                            <span class="dato-valor">{{ $pago->comprobante->estado_ocr }}</span>
                        </div>
                    </div>

                    {{-- Panel OCR --}}
                    @if($pago->comprobante)
                        <div class="ocr-panel ocr-panel--{{ strtolower($pago->comprobante->estado_ocr ?? 'pendiente') }}">
                            <div class="ocr-panel__header">
                                <span class="ocr-panel__titulo">Lectura OCR</span>
                                <span class="ocr-badge ocr-badge--{{ strtolower($pago->comprobante->estado_ocr ?? 'pendiente') }}">
                {{ $pago->comprobante->estado_ocr ?? 'PENDIENTE' }}
            </span>
                            </div>

                            @if($pago->comprobante->estado_ocr === 'PROCESADO')
                                <div class="ocr-grid">

                                    {{-- Monto --}}
                                    <div class="ocr-campo">
                                        <span class="ocr-label">Monto detectado</span>
                                        @php $coincide = $pago->comprobante->montoCoincideCon((float) $pago->monto); @endphp
                                        <span class="ocr-valor">
                        {{ $pago->comprobante->monto_detectado
                            ? 'RD$ ' . number_format($pago->comprobante->monto_detectado, 2)
                            : '—' }}
                                            @if($coincide === true)
                                                <span class="ocr-match ocr-match--ok" title="Coincide con el monto esperado">✓</span>
                                            @elseif($coincide === false)
                                                <span class="ocr-match ocr-match--error" title="No coincide con RD$ {{ number_format($pago->monto, 2) }}">✗</span>
                                            @endif
                    </span>
                                        <span class="ocr-esperado">Esperado: RD$ {{ number_format($pago->monto, 2) }}</span>
                                    </div>

                                    {{-- Fecha --}}
                                    <div class="ocr-campo">
                                        <span class="ocr-label">Fecha detectada</span>
                                        <span class="ocr-valor">
                        {{ $pago->comprobante->fecha_detectada
                            ? $pago->comprobante->fecha_detectada->format('d/m/Y')
                            : '—' }}
                    </span>
                                    </div>

                                    {{-- Banco --}}
                                    <div class="ocr-campo">
                                        <span class="ocr-label">Banco detectado</span>
                                        <span class="ocr-valor">{{ $pago->comprobante->banco_detectado ?? '—' }}</span>
                                    </div>

                                    {{-- Referencia --}}
                                    <div class="ocr-campo">
                                        <span class="ocr-label">Referencia</span>
                                        <span class="ocr-valor ocr-valor--mono">
                        {{ $pago->comprobante->referencia_detectada ?? '—' }}
                    </span>
                                    </div>

                                    {{-- Texto completo colapsable --}}
                                    @if(!empty($pago->comprobante->respuesta_ocr_raw['texto_completo']))
                                        <div class="ocr-campo ocr-campo--full">
                                            <button type="button" class="ocr-toggle"
                                                    onclick="toggleOcrTexto({{ $pago->id }})">
                                                Ver texto completo detectado ▾
                                            </button>
                                            <pre id="ocr-texto-{{ $pago->id }}"
                                                 class="ocr-texto"
                                                 style="display:none;">{{ $pago->comprobante->respuesta_ocr_raw['texto_completo'] }}</pre>
                                        </div>
                                    @endif

                                </div>
                            @elseif($pago->comprobante->estado_ocr === 'FALLIDO')
                                <p class="ocr-fallido-msg">
                                    El OCR no pudo leer este comprobante. Revisar manualmente la imagen.
                                </p>
                            @else
                                <p class="ocr-fallido-msg">Procesando...</p>
                            @endif
                        </div>
                    @endif

                    <div class="reserva-cliente">
                        <span class="dato-label">Información del Cliente</span>
                        <div class="reserva-grid-cliente">
                            <div class="reserva-dato">
                                <span class="dato-sublabel">Nombre y Apellido</span>
                                <span class="dato-valor">
                                    {{ $pago->reserva->cliente->usuario->persona->nombre ?? '—' }}
                                    {{ $pago->reserva->cliente->usuario->persona->apellido ?? '' }}
                                </span>
                            </div>
                            <div class="reserva-dato">
                                <span class="dato-sublabel">Correo</span>
                                <span class="dato-valor">{{ $pago->reserva->cliente->usuario->email ?? '—' }}</span>
                            </div>
                        </div>
                    </div>

                    {{-- Panel rechazo --}}
                    <div id="rechazo-{{ $pago->id }}" class="reserva-panel" style="display:none;">
                        <span class="dato-label">Motivo del Rechazo</span>
                        <form method="POST" action="{{ route('admin.pagos.rechazar', $pago) }}" class="reserva-panel-form">
                            @csrf
                            <div class="form-group">
                                <label class="panel-label">Indica el motivo *</label>
                                <textarea name="motivo" rows="4" class="panel-textarea"
                                          placeholder="Explica por qué se rechaza este comprobante..."
                                          required></textarea>
                            </div>
                            <div class="panel-actions">
                                <button type="submit" class="btn btn-danger">Confirmar Rechazo</button>
                                <button type="button" class="btn btn-outline" onclick="toggleRechazo({{ $pago->id }})">Cancelar</button>
                            </div>
                        </form>
                    </div>

                    {{-- Acciones --}}
                    <div id="actions-{{ $pago->id }}" class="reserva-actions">
                        <a href="{{ route('admin.comprobante.imagen', $pago->comprobante) }}"
                           class="glightbox btn btn-outline"
                           data-type="image">
                            🔍︎ Previsualizar comprobante
                        </a>

                        <form method="POST" action="{{ route('admin.pagos.aprobar', $pago) }}" class="reserva-panel-form">
                            @csrf
                            <button type="submit" class="btn btn-aprobar">✓ Aprobar</button>
                        </form>

                        <button type="button" class="btn btn-rechazar" onclick="toggleRechazo({{ $pago->id }})">
                            ✗ Rechazar
                        </button>
                    </div>

                </div>
            </div>
        @empty
        @endforelse
    </div>

    {{ $pagos->links() }}

@endsection

@push('scripts')
    @include('partials.zoom')
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            iniciarLightboxConZoom('.glightbox');
        });

        function toggleRechazo(id) {
            const panel = document.getElementById('rechazo-' + id);
            const isHidden = panel.style.display === 'none' || panel.style.display === '';
            panel.style.display = isHidden ? 'block' : 'none';
            const actionGroup = document.getElementById('actions-' + id);
            actionGroup.querySelectorAll('button').forEach(btn => {
                btn.disabled = isHidden;
                btn.style.opacity = isHidden ? '0.4' : '1';
                btn.style.pointerEvents = isHidden ? 'none' : 'auto';
            });
            if (isHidden) panel.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
        }

        function toggleOcrTexto(id) {
            const el = document.getElementById('ocr-texto-' + id);
            const btn = el.previousElementSibling;
            const visible = el.style.display !== 'none';
            el.style.display = visible ? 'none' : 'block';
            btn.textContent = visible ? 'Ver texto completo detectado ▾' : 'Ocultar texto ▴';
        }
    </script>
@endpush
