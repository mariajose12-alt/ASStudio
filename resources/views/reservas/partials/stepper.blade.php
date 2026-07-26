@php
    // $step: paso lógico actual (1, 2, 3 o 4)
    // $requiereTelefono: si el usuario necesita completar el paso de teléfono
    $pasos = $requiereTelefono
        ? ['Sesión', 'Fecha', 'Datos', 'Resumen']
        : ['Sesión', 'Fecha', 'Resumen'];

    // Si no requiere teléfono, el paso "4" (resumen) pasa a ser el "3" visualmente
    $stepVisual = (!$requiereTelefono && $step === 4) ? 3 : $step;
@endphp

<div class="stepper">
    @foreach ($pasos as $index => $label)
        @php $numero = $index + 1; @endphp

        <div class="stepper-step">
            <div class="stepper-circle {{ $numero < $stepVisual ? 'done' : ($numero === $stepVisual ? 'active' : '') }}">
                {{ $numero < $stepVisual ? '✓' : $numero }}
            </div>
            <span class="stepper-label {{ $numero === $stepVisual ? 'active' : '' }}">{{ $label }}</span>
        </div>

        @if (!$loop->last)
            <div class="stepper-line {{ $numero < $stepVisual ? 'done' : '' }}"></div>
        @endif
    @endforeach
</div>
