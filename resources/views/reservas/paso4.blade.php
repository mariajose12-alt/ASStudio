@extends('layouts.reserva')

@section('formulario')

    <div class="reserva-logo">AS <span>Studio</span></div>

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

    {{-- Info de fecha y tipo --}}
    <div class="d-flex gap-2 mb-3">
        <span class="badge bg-light text-dark border">{{ \Carbon\Carbon::parse($paso2['fecha'])->format('d/m/Y') }}</span>
        <span class="badge bg-light text-dark border">{{ $paso2['hora'] }}</span>
        <span class="badge bg-light text-dark border">{{ $paso1['tipo'] }}</span>
    </div>

    {{-- Detalle sesión --}}
    <div class="card mb-3 border-0 bg-light rounded-3 p-3">
        <p class="mb-1 fw-bold">Sesión: {{ $paquete->catalogos->first()?->nombre ?? '—' }} · {{ $paquete->nombre }}</p>
        <p class="mb-0 text-muted small">{{ $paso2['descripcion'] }}</p>
    </div>

    {{-- Precio --}}
    <div class="card mb-4 border-0 bg-light rounded-3 p-3">
        <p class="text-muted small mb-1" style="font-size:0.7rem; text-transform:uppercase; letter-spacing:0.08em;">Precio Estimado</p>
        <h4 class="fw-bold mb-0" style="color:var(--sage-lt);">${{ number_format($paquete->precio_base, 2) }}</h4>
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
                <a href="/terminos-condiciones" style="color:var(--sage);">Leer términos completos →</a>
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
