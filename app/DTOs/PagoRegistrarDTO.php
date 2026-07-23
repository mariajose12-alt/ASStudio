<?php

namespace App\DTOs;

use App\Exceptions\NegocioException;
use Illuminate\Http\Request;

class PagoRegistrarDTO
{
    private const METODOS_VALIDOS = ['TRANSFERENCIA', 'TARJETA', 'EFECTIVO'];

    public function __construct(
        public readonly string $metodo,
    ) {
        if (!in_array($this->metodo, self::METODOS_VALIDOS, true)) {
            throw new NegocioException("Método de pago no válido: {$this->metodo}");
        }
    }

    public static function fromRequest(Request $request): self
    {
        return new self(
            metodo: $request->string('metodo'),
        );
    }
}
