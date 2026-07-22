<?php

namespace App\DTOs;

use App\Exceptions\NegocioException;
use Illuminate\Http\Request;

class AccionReservaDTO
{
    private const ACCIONES_VALIDAS = ['APROBADA', 'RECHAZADA', 'MODIFICACION_PROPUESTA', 'CERRAR_SESION'];

    public function __construct(
        public readonly string  $accion,
        public readonly ?string $motivo = null,
        public readonly ?string $nueva_fecha = null,
        public readonly ?string $nueva_hora  = null,
        public readonly float   $duracion_horas = 2.0,
    ) {
        if (!in_array($this->accion, self::ACCIONES_VALIDAS, true)) {
            throw new NegocioException("Acción no válida: {$this->accion}");
        }
    }

    public static function fromRequest(Request $request): self
    {
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
