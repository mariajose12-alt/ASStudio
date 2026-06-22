<?php

namespace App\DTOs;

use Carbon\Carbon;
use Illuminate\Http\Request;

class NominaCalculoDTO
{
    public function __construct(
        public readonly string $periodo,
        public readonly Carbon $fechaInicio,
        public readonly Carbon $fechaFin,
        public readonly int    $creadaPorId,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            periodo:     $request->string('periodo'),
            fechaInicio: Carbon::parse($request->fecha_inicio)->startOfDay(),
            fechaFin:    Carbon::parse($request->fecha_fin)->endOfDay(),
            creadaPorId: (int) $request->creada_por_id,
        );
    }
}
