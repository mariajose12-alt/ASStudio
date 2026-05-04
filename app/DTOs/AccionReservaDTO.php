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
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            accion:      $request->string('accion'),
            motivo:      $request->string('motivo')      ?: null,
            nueva_fecha: $request->string('nueva_fecha') ?: null,
            nueva_hora:  $request->string('nueva_hora')  ?: null,
        );
    }
}
