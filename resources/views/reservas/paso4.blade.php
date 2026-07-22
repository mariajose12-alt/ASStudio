@extends('layouts.reserva')

@section('formulario')

    <div class="reserva-logo">
        <a href="/" class="navbar-logo">
            <img src="{{ asset('images/logo.png') }}" alt="AStudio" height="45">
        </a>
    </div>

    {{-- Stepper --}}
    <div class="stepper">
        <div class="stepper-step">
            <div class="stepper-circle done">✓</div>
            <span class="stepper-label">Sesión</span>
        </div>
        <div class="stepper-line done"></div>
        <div class="stepper-step">
            <div class="stepper-circle done">✓</div>
            <span class="stepper-label">Fecha</span>
        </div>
        <div class="stepper-line done"></div>
        <div class="stepper-step">
            <div class="stepper-circle done">✓</div>
            <span class="stepper-label">Datos</span>
        </div>
        <div class="stepper-line done"></div>
        <div class="stepper-step">
            <div class="stepper-circle active">4</div>
            <span class="stepper-label active">Resumen</span>
        </div>
    </div>

    <h5>Paso 4 de 4</h5>
    <h4>Resumen de tu Reserva</h4>

    @if(session('error'))
        <div style="background:#fdf0ef; border:1px solid #e8b4b0; border-radius:8px; padding:14px 16px; margin-bottom:16px; font-size:13px; color:#c0392b;">
            {{ session('error') }}
        </div>
    @endif

    {{-- Info de fecha, hora y lugar --}}
    <div class="resumen-meta-strip">
        <div class="resumen-meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 3v2.25M17.25 3v2.25M3 18.75V7.5a2.25 2.25 0 0 1 2.25-2.25h13.5A2.25 2.25 0 0 1 21 7.5v11.25m-18 0A2.25 2.25 0 0 0 5.25 21h13.5A2.25 2.25 0 0 0 21 18.75m-18 0v-7.5A2.25 2.25 0 0 1 5.25 9h13.5A2.25 2.25 0 0 1 21 11.25v7.5" />
            </svg>
            <span>{{ \Carbon\Carbon::parse($paso2['fecha'])->translatedFormat('d \d\e F, Y') }}</span>
        </div>
        <div class="resumen-meta-divider"></div>
        <div class="resumen-meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <span>{{ $paso2['hora'] }}</span>
        </div>
        <div class="resumen-meta-divider"></div>
        <div class="resumen-meta-item">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 10.5a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 10.5c0 7.142-7.5 11.25-7.5 11.25S4.5 17.642 4.5 10.5a7.5 7.5 0 1 1 15 0Z" />
            </svg>
            <span>{{ $paso1['tipo'] === 'EXTERIOR' ? ($paso1['lugar'] ?? 'Exterior') : 'Estudio' }}</span>
        </div>
    </div>

    {{-- Card sesión --}}
    <div class="resumen-card-sesion">
        <div class="resumen-card-sesion__header">
            <div class="resumen-card-sesion__icon">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.827 6.175A2.31 2.31 0 0 1 5.186 7.23c-.38.054-.757.112-1.134.175C2.999 7.58 2.25 8.507 2.25 9.574V18a2.25 2.25 0 0 0 2.25 2.25h15A2.25 2.25 0 0 0 21.75 18V9.574c0-1.067-.75-1.994-1.802-2.169a47.865 47.865 0 0 0-1.134-.175 2.31 2.31 0 0 1-1.64-1.055l-.822-1.316a2.192 2.192 0 0 0-1.736-1.039 48.774 48.774 0 0 0-5.232 0 2.192 2.192 0 0 0-1.736 1.039l-.821 1.316Z" />
                    <path stroke-linecap="round" stroke-linejoin="round" d="M16.5 12.75a4.5 4.5 0 1 1-9 0 4.5 4.5 0 0 1 9 0ZM18.75 10.5h.008v.008h-.008V10.5Z" />
                </svg>
            </div>
            <div class="resumen-card-sesion__meta">
                <div>
                    <p class="resumen-card-sesion__eyebrow">{{ $paquete->catalogos->first()?->nombre ?? '—' }}</p>
                    <p class="resumen-card-sesion__title">{{ $paquete->nombre }}</p>
                </div>
                <div class="resumen-card-sesion__fotos">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.8" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 15.75 5.159-5.159a2.25 2.25 0 0 1 3.182 0l5.159 5.159m-1.5-1.5 1.409-1.409a2.25 2.25 0 0 1 3.182 0l2.909 2.909m-18 3.75h16.5a1.5 1.5 0 0 0 1.5-1.5V6a1.5 1.5 0 0 0-1.5-1.5H3.75A1.5 1.5 0 0 0 2.25 6v12a1.5 1.5 0 0 0 1.5 1.5Zm10.5-11.25h.008v.008h-.008V8.25Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                    </svg>
                    {{ $paquete->cantidad_fotos_incluidas }} fotos incluidas
                </div>
            </div>
        </div>
        <div class="resumen-card-sesion__body">
            <p class="resumen-card-sesion__desc">{{ $paso2['descripcion'] }}</p>
        </div>
    </div>

    {{-- Card precio --}}
    <div class="resumen-card-precio">
        <div>
            <p class="resumen-card-precio__label">Precio Estimado</p>
            <p class="resumen-card-precio__note">Sujeto a confirmación</p>
        </div>
        <div class="resumen-card-precio__amount">
            <span class="resumen-card-precio__currency">DOP</span>
            <span class="resumen-card-precio__value">${{ number_format($paquete->precio_base, 2) }}</span>
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('cliente.reservas.paso3') }}" class="btn btn-outline-secondary btn-sm">← Editar</a>
        {{-- Este botón abre el modal, NO hace submit directo --}}
        <button type="button" class="btn-reserva" onclick="document.getElementById('modalConfirmar').style.display='flex'">
            Enviar Solicitud
        </button>
    </div>

    {{-- ===== MODAL ===== --}}
    <div id="modalConfirmar" style="
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(0,0,0,0.55);
        z-index: 9999;
        align-items: center;
        justify-content: center;
    ">
        <div style="
            background: #fff;
            border-radius: 16px;
            padding: 2rem;
            max-width: 480px;
            width: 90%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.2);
        ">
            <h4 class="fw-bold mb-1">Antes de Enviar</h4>
            <hr class="mb-3">

            <p class="fw-bold small mb-3">¿Qué pasa ahora?</p>

            <div class="d-flex gap-3 mb-3 align-items-start">
                <span style="background:#111; color:#fff; border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:0.75rem; flex-shrink:0;">1</span>
                <p class="mb-0 small">Tu solicitud es revisada por el equipo en un plazo de <strong>24 horas hábiles</strong></p>
            </div>

            <div class="d-flex gap-3 mb-3 align-items-start">
                <span style="background:#111; color:#fff; border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:0.75rem; flex-shrink:0;">2</span>
                <p class="mb-0 small">Recibirás una <strong>notificación por email</strong> con la confirmación o ajustes</p>
            </div>

            <div class="d-flex gap-3 mb-4 align-items-start">
                <span style="background:#111; color:#fff; border-radius:50%; width:24px; height:24px; display:flex; align-items:center; justify-content:center; font-size:0.75rem; flex-shrink:0;">3</span>
                <p class="mb-0 small">Tras la confirmación, se emitirá la factura y el pago deberá efectuarse <strong>dentro del plazo establecido.</strong></p>
            </div>

            <p class="small text-muted mb-3">
                Al enviar la solicitud aceptas nuestras condiciones de reserva, política de cancelación y uso del espacio.
                <a href="javascript:void(0)" onclick="document.getElementById('modalTerminos').style.display='flex'" style="color:var(--sage);">Leer términos completos →</a>
            </p>

            <div class="terminos-condiciones">
                <input type="checkbox" id="aceptoTerminos" name="acepto_terminos">
                <label for="aceptoTerminos">Entiendo y acepto los términos y condiciones</label>
            </div>

            <hr class="mb-3">

            {{-- Formulario real que se envía solo al confirmar --}}
            <form method="POST" action="{{ route('cliente.reservas.enviar') }}" id="formEnviar">
                @csrf
            </form>

            <div class="d-flex gap-2 justify-content-end">
                <button type="button" class="btn btn-outline-secondary"
                        onclick="document.getElementById('modalConfirmar').style.display='none'">
                    Cancelar
                </button>
                <button type="button" class="btn-reserva" id="btnConfirmar" disabled
                        onclick="document.getElementById('formEnviar').submit()">
                    Confirmar
                </button>
            </div>
        </div>
    </div>
    {{-- MODAL TÉRMINOS --}}
    <div id="modalTerminos" style="
    display: none;
    position: fixed;
    inset: 0;
    background: rgba(0,0,0,0.55);
    z-index: 10000;
    align-items: center;
    justify-content: center;
">
        <div style="
        background: #fff;
        border-radius: 16px;
        max-height: 85vh;
        max-width: 680px;
        width: 90%;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0,0,0,0.3);
    ">
            <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid #eee; display:flex; justify-content:space-between; align-items:center;">
                <strong>Términos y Condiciones</strong>
                <button onclick="document.getElementById('modalTerminos').style.display='none'"
                        style="background:none; border:none; font-size:1.4rem; cursor:pointer; color:#888;">&times;</button>
            </div>
            <div style="overflow-y: auto; padding: 1.5rem; flex:1;">
                @include('partials.terms-content')
            </div>
            <div style="padding: 1rem 1.5rem; border-top: 1px solid #eee; text-align:right;">
                <button onclick="document.getElementById('modalTerminos').style.display='none'"
                        class="btn btn-outline-secondary btn-sm">Cerrar</button>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Habilitar botón Confirmar solo si acepta términos
            document.getElementById('aceptoTerminos').addEventListener('change', function () {
                document.getElementById('btnConfirmar').disabled = !this.checked;
            });

            // Cerrar modal al hacer clic fuera
            document.getElementById('modalConfirmar').addEventListener('click', function (e) {
                if (e.target === this) this.style.display = 'none';
            });
        });
    </script>
@endpush
