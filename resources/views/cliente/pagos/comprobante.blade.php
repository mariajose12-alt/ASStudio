@extends('layouts.cliente')

@section('title', 'Subir comprobante de pago')

@section('content')
    <div class="cp-page">

        {{-- ── PASO A: Selector de plan (solo ANTICIPO) ── --}}
        @if($pago->tipo === 'ANTICIPO')
            @php
                $total   = $pago->reserva->precio_total;
                $anticipo = $pago->monto;
            @endphp
            <div id="pasoA" class="cp-paso-a">
                <div class="cp-left">
                    <svg class="cp-hero-card__deco" viewBox="0 0 180 180" fill="none" aria-hidden="true">
                        <circle cx="160" cy="20" r="70" stroke="#fff3e8" stroke-width="1.5"/>
                        <circle cx="160" cy="20" r="100" stroke="#fff3e8" stroke-width="1"/>
                        <circle cx="160" cy="20" r="130" stroke="#fff3e8" stroke-width="0.8"/>
                        <circle cx="40" cy="160" r="40" stroke="#e87722" stroke-width="1"/>
                        <circle cx="40" cy="160" r="60" stroke="#e87722" stroke-width="0.6"/>
                    </svg>

                    <div class="cp-hero-card__eyebrow">Tu sesión</div>
                    <div class="cp-hero-card__catalogo">{{ $pago->reserva->catalogo->nombre ?? 'Sesión' }}</div>
                    <div class="cp-hero-card__paquete">
                        {{ $pago->reserva->paquete->nombre ?? 'Sesión fotográfica' }}
                        @if($pago->reserva->paquete->cantidad_fotos_incluidas)
                            · {{ $pago->reserva->paquete->cantidad_fotos_incluidas }} fotos
                        @endif
                    </div>

                    <hr class="cp-hero-card__divider">

                    <div class="cp-hero-card__meta">
                        <div class="cp-hero-card__pill">
                            <svg width="13" height="13" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect x="3" y="4" width="18" height="18" rx="2"/><path d="M16 2v4M8 2v4M3 10h18"/></svg>
                            {{ \Carbon\Carbon::parse($pago->reserva->fecha_inicio)->translatedFormat('d M') }}
                        </div>
                    </div>
                </div>

                <div class="cp-right">
                    <div class="cp-right__titulo">¿Cuánto quieres pagar hoy?</div>
                    <div class="cp-right__sub">Elige el plan que mejor te funcione.</div>
                </div>

                <div class="cp-opciones">
                    <button type="button" class="cp-opcion" id="btnAnticipo"
                            onclick="elegirPlan('anticipo', {{ number_format($anticipo, 2, '.', '') }})">
                        <div class="cp-opcion__top">
                            <span class="cp-opcion__nombre">Anticipo</span>
                            <span class="cp-opcion__badge">50%</span>
                        </div>
                        <div class="cp-opcion__monto">RD$ {{ number_format($anticipo, 2) }}</div>
                        <div class="cp-opcion__detalle">Reserva tu fecha hoy. Pagas el resto después de tu sesión.</div>
                    </button>

                    <button type="button" class="cp-opcion" id="btnCompleto"
                            onclick="elegirPlan('completo', {{ number_format($total, 2, '.', '') }})">
                        <div class="cp-opcion__top">
                            <span class="cp-opcion__nombre">Pago completo</span>
                            <span class="cp-opcion__badge cp-opcion__badge--gold">100%</span>
                        </div>
                        <div class="cp-opcion__monto">RD$ {{ number_format($total, 2) }}</div>
                        <div class="cp-opcion__detalle">Cubre todo hoy y olvídate del pago final.</div>
                    </button>
                </div>

                <button type="button" class="cp-btn-continuar" id="btnContinuar" disabled
                        onclick="continuarAlPago()">
                    Continuar
                </button>
            </div>
        @endif

        {{-- ── PASO B: Formulario de pago ── --}}
        <div id="pasoB" class="{{ $pago->tipo === 'ANTICIPO' ? 'cp-paso-b--oculto' : '' }}">

            {{-- Resumen compacto --}}
            <div class="cp-resumen">
                <div class="cp-resumen__paquete">{{ $pago->reserva->paquete->nombre ?? 'Sesión fotográfica' }}</div>
                @php
                    $etiquetas = [
                        'ANTICIPO' => 'Anticipo de reserva',
                        'FINAL'    => 'Pago final',
                        'COMPLETO' => 'Pago completo',
                    ];
                @endphp
                <div class="cp-resumen__tipo" id="resumenTipo">
                    {{ $etiquetas[$pago->tipo] ?? ucfirst(strtolower($pago->tipo)) }}
                </div>
                <div class="cp-resumen__monto" id="resumenMonto">
                    RD$ {{ number_format($pago->monto, 2) }}
                </div>
            </div>

            {{-- Alertas --}}
            @if($pago->estado === 'RECHAZADO' && $pago->motivo_rechazo)
                <div class="cp-alerta cp-alerta--error">
                    <svg width="16" height="16" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                    <div>
                        <strong>Comprobante rechazado</strong>
                        <p>{{ $pago->motivo_rechazo }}</p>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div class="cp-alerta cp-alerta--error">{{ session('error') }}</div>
            @endif

            @error('comprobante')
            <div class="cp-alerta cp-alerta--error">{{ $message }}</div>
            @enderror

            {{-- Pasos --}}
            <div class="cp-pasos">
                <div class="cp-paso">
                    <div class="cp-paso__num">1</div>
                    <div class="cp-paso__body">
                        <div class="cp-paso__titulo">Copia los datos bancarios</div>
                        <p class="cp-paso__desc">
                            Transfiere exactamente <strong id="monto-transferir">RD$ {{ number_format($pago->monto, 2) }}</strong> a una de nuestras cuentas.
                        </p>
                        <button type="button" data-modal="cuentas-banco" class="cp-btn-cuentas">
                            <svg width="14" height="14" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2">
                                <rect x="3" y="8" width="18" height="11" rx="2"/>
                                <path d="M7 8V6a5 5 0 0 1 10 0v2"/>
                            </svg>
                            Ver cuentas disponibles
                        </button>
                    </div>
                </div>

                <div class="cp-paso__conector"></div>

                <div class="cp-paso">
                    <div class="cp-paso__num">2</div>
                    <div class="cp-paso__body">
                        <div class="cp-paso__titulo">Realiza la transferencia</div>
                        <p class="cp-paso__desc">Desde tu app o banca en línea. Guarda la pantalla de confirmación — la necesitarás en el siguiente paso.</p>
                    </div>
                </div>

                <div class="cp-paso__conector"></div>

                <div class="cp-paso">
                    <div class="cp-paso__num">3</div>
                    <div class="cp-paso__body">
                        <div class="cp-paso__titulo">Sube el comprobante</div>
                        <p class="cp-paso__desc">Una captura de pantalla o foto clara de la confirmación de transferencia.</p>

                        <form method="POST"
                              action="{{ route('cliente.pagos.comprobante.guardar', $pago->id) }}"
                              enctype="multipart/form-data"
                              id="formComprobante">
                            @csrf
                            <input type="hidden" name="plan_pago" id="inputPlanPago"
                                   value="{{ $pago->tipo === 'ANTICIPO' ? 'anticipo' : '' }}">

                            <label for="comprobante" class="cp-dropzone" id="dropzone">
                                <input type="file" name="comprobante" id="comprobante" accept=".jpg,.jpeg,.png" hidden>
                                <div id="dropzoneContenido" class="cp-dropzone__contenido">
                                    <svg width="28" height="28" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="1.5">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4M17 8l-5-5-5 5M12 3v12"/>
                                    </svg>
                                    <span class="cp-dropzone__texto">Toca para seleccionar</span>
                                    <span class="cp-dropzone__hint">JPG o PNG · Máx. 8 MB</span>
                                </div>
                                <img id="previsualizacion" class="cp-preview" style="display:none;" alt="Vista previa del comprobante">
                            </label>

                            <span id="dropzoneError" class="cp-dropzone__error" style="display:none;"></span>

                            <button type="submit" class="cp-btn-enviar" id="btnEnviar" disabled>
                                Enviar comprobante
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <p class="cp-nota">
                Tu reserva se confirmará automáticamente una vez que nuestro equipo valide la transferencia. Esto tarda menos de 24 horas.
            </p>
        </div>

    </div>

    <style>
        .cp-page {
            margin: 0 auto;
            padding: 24px 16px 80px;
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        /* ── Paso A ── */
        .cp-paso-a {
            display: grid;
            grid-template-columns: 1fr;
            gap: 20px;
        }

        @media (min-width: 768px) {
            .cp-paso-a {
                grid-template-columns: 1fr 1fr;
                grid-template-areas:
            "left     pregunta"
            "left     opciones"
            "continuar continuar";
                align-items: start;
            }

            .cp-left  { grid-area: left; height: 100%; }
            .cp-right { grid-area: pregunta; }

            .cp-opciones {
                justify-content: center;
            }
        }

        .cp-layout {
            display: grid;
            grid-template-columns: 1fr;
            min-height: 100vh;
        }

        @media (min-width: 768px) {
            .cp-layout {
                grid-template-columns: 1fr 1fr;
            }
        }

        .cp-left {
            background: #1a0f00;
            padding: 48px 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
            border-radius: 16px;
        }

        .cp-right {
            background: #f7f1eb;
            padding: 48px 36px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 16px;
            border-radius: 16px;
        }

        .cp-hero-card__deco {
            position: absolute; top: 0; right: 0;
            width: 180px; height: 180px;
            opacity: 0.07; pointer-events: none;
        }
        .cp-hero-card__eyebrow {
            font-size: 11px; font-weight: 600;
            letter-spacing: 0.08em; text-transform: uppercase;
            color: #e87722; margin-bottom: 10px;
        }
        .cp-hero-card__catalogo {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 24px; font-weight: 700;
            color: #fff3e8; line-height: 1.15; margin-bottom: 4px;
        }
        .cp-hero-card__paquete { font-size: 13px; color: rgba(255,243,232,0.55); margin-bottom: 18px; }
        .cp-hero-card__divider { border: none; border-top: 1px solid rgba(255,243,232,0.1); margin-bottom: 16px; }
        .cp-hero-card__meta { display: flex; align-items: center; gap: 8px; }
        .cp-hero-card__pill {
            display: inline-flex; align-items: center; gap: 5px;
            background: rgba(232,119,34,0.15);
            border: 1px solid rgba(232,119,34,0.3);
            border-radius: 999px; padding: 5px 12px;
            font-size: 12px; font-weight: 600; color: #e87722;
        }
        .cp-pregunta { padding: 2px 4px; }
        .cp-pregunta__titulo { font-size: 17px; font-weight: 600; color: #1a0f00; margin-bottom: 4px; }
        .cp-pregunta__sub { font-size: 13px; color: #9e8c7e; }

        .cp-opciones {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .cp-opcion {
            display: flex;
            flex-direction: column;
            gap: 6px;
            padding: 18px 18px;
            background: #fff;
            border: 2px solid #f0e6da;
            border-radius: 16px;
            cursor: pointer;
            text-align: left;
            transition: border-color 0.18s, background 0.18s, transform 0.15s;
            width: 100%;
        }

        .cp-btn-continuar {
            grid-column: 1 / -1;
        }

        .cp-opcion:hover {
            border-color: #e87722;
            background: #fffaf6;
            transform: translateY(-1px);
        }

        .cp-opcion.seleccionada {
            border-color: #e87722;
            background: #fff3e8;
        }

        .cp-opcion__top {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .cp-paso-a__subpaquete {
            font-size: 14px;
            font-weight: 600;
            color: #6b5c4e;
        }

        .cp-opcion__nombre {
            font-size: 15px;
            font-weight: 700;
            color: #1a0f00;
        }

        .cp-opcion__badge {
            font-size: 11px;
            font-weight: 700;
            padding: 3px 10px;
            border-radius: 999px;
            background: #fff3e8;
            color: #e87722;
            letter-spacing: 0.04em;
        }

        .cp-opcion__badge--gold {
            background: #1a0f00;
            color: #fff3e8;
        }

        .cp-opcion__monto {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 28px;
            font-weight: 700;
            color: #1a0f00;
            line-height: 1;
        }

        .cp-opcion__detalle {
            font-size: 12px;
            color: #9e8c7e;
            line-height: 1.4;
        }

        .cp-btn-continuar {
            width: 100%;
            background: #e87722;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 16px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .cp-btn-continuar:disabled {
            opacity: 0.35;
            cursor: not-allowed;
        }

        .cp-btn-continuar:not(:disabled):hover {
            opacity: 0.9;
            transform: translateY(-1px);
        }

        /* ── Paso B ── */
        .cp-paso-b--oculto { display: none; }

        /* Resumen */
        .cp-resumen {
            background: #1a0f00;
            border-radius: 18px;
            padding: 20px 22px;
            color: #fff;
            display: flex;
            flex-direction: column;
            gap: 4px;
            margin-bottom: 40px;
        }

        .cp-resumen__paquete {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 16px;
            font-weight: 700;
            line-height: 1.2;
            opacity: 0.7;
        }

        .cp-resumen__tipo {
            font-size: 11px;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.06em;
            color: #e87722;
        }

        .cp-resumen__monto {
            font-family: 'Playfair Display', Georgia, serif;
            font-size: 30px;
            font-weight: 700;
            margin-top: 4px;
            color: #fff3e8;
        }

        /* Alertas */
        .cp-alerta {
            border-radius: 12px;
            padding: 14px 16px;
            font-size: 13px;
            line-height: 1.5;
            display: flex;
            gap: 10px;
            align-items: flex-start;
        }

        .cp-alerta--error {
            background: #fdecea;
            border: 1px solid rgba(217,48,37,0.2);
            color: #9a2a1c;
            margin-bottom: 25px;
        }

        .cp-alerta strong { display: block; font-weight: 700; margin-bottom: 2px; }
        .cp-alerta p { margin: 0; }

        .cp-dropzone__error {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: 8px;
            font-size: 12.5px;
            font-weight: 600;
            color: #c0392b;
        }

        /* Pasos */
        .cp-pasos { display: flex; flex-direction: column; }

        .cp-paso { display: flex; gap: 14px; align-items: flex-start; }

        .cp-paso__conector {
            width: 28px;
            display: flex;
            justify-content: center;
            flex-shrink: 0;
            padding: 4px 0;
        }

        .cp-paso__conector::before {
            content: '';
            display: block;
            width: 2px;
            height: 24px;
            background: #f0e6da;
            border-radius: 2px;
        }

        .cp-paso__num {
            width: 28px;
            height: 28px;
            border-radius: 50%;
            background: #e87722;
            color: #fff;
            font-size: 13px;
            font-weight: 800;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            margin-top: 1px;
        }

        .cp-paso__body { flex: 1; padding-bottom: 8px; }

        .cp-paso__titulo {
            font-size: 15px;
            font-weight: 700;
            color: #1a0f00;
            margin-bottom: 4px;
        }

        .cp-paso__desc {
            font-size: 13px;
            color: #6b5c4e;
            margin: 0 0 12px;
            line-height: 1.5;
        }

        .cp-btn-cuentas {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #fff3e8;
            border: 1px solid rgba(232,119,34,0.35);
            border-radius: 999px;
            padding: 9px 16px;
            font-size: 13px;
            font-weight: 600;
            color: #e87722;
            cursor: pointer;
            transition: background 0.15s;
        }

        .cp-btn-cuentas:hover { background: #ffe8cc; }

        .cp-dropzone {
            display: block;
            border: 2px dashed rgba(232,119,34,0.35);
            border-radius: 14px;
            padding: 28px 16px;
            text-align: center;
            cursor: pointer;
            transition: border-color 0.2s, background 0.2s;
            margin-bottom: 14px;
        }

        .cp-dropzone:hover, .cp-dropzone.dragover {
            border-color: #e87722;
            background: #fff3e8;
        }

        .cp-dropzone__contenido {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 6px;
            color: #9e8c7e;
        }

        .cp-dropzone__texto { font-size: 14px; font-weight: 600; color: #1a0f00; }
        .cp-dropzone__hint { font-size: 12px; color: #b0a090; }

        .cp-preview {
            max-width: 100%;
            max-height: 260px;
            border-radius: 10px;
            display: block;
            margin: 0 auto;
        }

        .cp-btn-enviar {
            width: 100%;
            background: #e87722;
            color: #fff;
            border: none;
            border-radius: 999px;
            padding: 16px;
            font-size: 15px;
            font-weight: 700;
            cursor: pointer;
            transition: opacity 0.2s, transform 0.15s;
        }

        .cp-btn-enviar:not(:disabled):hover { opacity: 0.9; transform: translateY(-1px); }
        .cp-btn-enviar:disabled { opacity: 0.35; cursor: not-allowed; }

        .cp-nota {
            font-size: 12px;
            color: #b0a090;
            text-align: center;
            line-height: 1.5;
            margin: 0;
            padding: 0 8px;
        }
    </style>

    <script>
        let planElegido = null;

        function elegirPlan(plan, monto) {
            planElegido = plan;

            document.querySelectorAll('.cp-opcion').forEach(el => el.classList.remove('seleccionada'));
            document.getElementById(plan === 'anticipo' ? 'btnAnticipo' : 'btnCompleto')
                .classList.add('seleccionada');

            document.getElementById('btnContinuar').disabled = false;
            document.getElementById('btnContinuar').dataset.monto = monto;
            document.getElementById('btnContinuar').dataset.plan  = plan;
        }

        function continuarAlPago() {
            const plan  = document.getElementById('btnContinuar').dataset.plan;
            const monto = parseFloat(document.getElementById('btnContinuar').dataset.monto);

            const montoFormateado = 'RD$ ' + monto.toLocaleString('es-DO', {
                minimumFractionDigits: 2,
                maximumFractionDigits: 2
            });

            // Actualizar resumen
            document.getElementById('resumenMonto').textContent = montoFormateado;
            document.getElementById('resumenTipo').textContent  =
                plan === 'completo' ? 'Pago completo' : 'Anticipo de reserva';
            document.getElementById('monto-transferir').textContent = montoFormateado;

            // Guardar plan en el input hidden del form
            document.getElementById('inputPlanPago').value = plan;

            // Transición
            document.getElementById('pasoA').style.opacity    = '0';
            document.getElementById('pasoA').style.transform  = 'translateY(-10px)';
            document.getElementById('pasoA').style.transition = 'opacity 0.2s ease, transform 0.2s ease';

            setTimeout(() => {
                document.getElementById('pasoA').style.display = 'none';
                const pasoB = document.getElementById('pasoB');
                pasoB.classList.remove('cp-paso-b--oculto');
                pasoB.style.opacity   = '0';
                pasoB.style.transform = 'translateY(10px)';
                pasoB.style.transition = 'opacity 0.25s ease, transform 0.25s ease';
                requestAnimationFrame(() => {
                    pasoB.style.opacity   = '1';
                    pasoB.style.transform = 'translateY(0)';
                });
            }, 200);
        }

        // Dropzone
        const input     = document.getElementById('comprobante');
        const dropzone  = document.getElementById('dropzone');
        const contenido = document.getElementById('dropzoneContenido');
        const preview   = document.getElementById('previsualizacion');
        const btnEnviar = document.getElementById('btnEnviar');
        const errorEl   = document.getElementById('dropzoneError');

        const MAX_BYTES = 8 * 1024 * 1024; // 8MB, igual que la validación del servidor

        function mostrarErrorArchivo(mensaje) {
            errorEl.textContent   = mensaje;
            errorEl.style.display = 'flex';
            dropzone.classList.add('cp-dropzone--error');
            btnEnviar.disabled    = true;
            preview.style.display = 'none';
            contenido.style.display = 'flex';
            input.value = '';
        }

        function limpiarErrorArchivo() {
            errorEl.style.display = 'none';
            dropzone.classList.remove('cp-dropzone--error');
        }

        input.addEventListener('change', () => {
            const archivo = input.files[0];
            if (!archivo) return;

            if (archivo.size > MAX_BYTES) {
                const pesoMB = (archivo.size / (1024 * 1024)).toFixed(1);
                mostrarErrorArchivo(`Esa imagen pesa ${pesoMB}MB y el máximo permitido es 8MB. Prueba con una captura de pantalla o comprime la foto.`);
                return;
            }

            limpiarErrorArchivo();
            btnEnviar.disabled = false;
            const lector = new FileReader();
            lector.onload = e => {
                preview.src = e.target.result;
                preview.style.display = 'block';
                contenido.style.display = 'none';
            };
            lector.readAsDataURL(archivo);
        });

        dropzone.addEventListener('dragover', e => { e.preventDefault(); dropzone.classList.add('dragover'); });
        dropzone.addEventListener('dragleave', () => dropzone.classList.remove('dragover'));
        dropzone.addEventListener('drop', e => {
            e.preventDefault();
            dropzone.classList.remove('dragover');
            if (e.dataTransfer.files.length) {
                input.files = e.dataTransfer.files;
                input.dispatchEvent(new Event('change'));
            }
        });
    </script>
@endsection
