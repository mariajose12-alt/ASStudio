<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class AccionReservaDTO
{
    public function __construct(
        public readonly string  $accion,       // APROBAR, RECHAZAR, MODIFICAR
        public readonly ?string $motivo = null, // requerido si rechaza o modifica
        public readonly ?string $nueva_fecha = null,
        public readonly ?string $nueva_hora  = null,
        public readonly float   $duracion_horas = 2.0,
    ) {}

    public static function fromRequest(Request $request): self
    {
        // Si el fotógrafo elige "personalizada", usa ese valor; si no, 2h estándar
        $duracion = $request->input('duracion_tipo') === 'personalizada'
            ? (float) $request->input('duracion_horas', 2.0)
            : 2.0;

        return new self(
            accion:      $request->string('accion'),
            motivo:      $request->string('motivo')      ?: null,
            nueva_fecha: $request->string('nueva_fecha') ?: null,
            nueva_hora:  $request->string('nueva_hora')  ?: null,
            duracion_horas:  $duracion,
        );
    }
}
