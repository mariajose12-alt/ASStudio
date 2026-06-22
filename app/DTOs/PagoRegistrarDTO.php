<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class PagoRegistrarDTO
{
    public function __construct(
        public readonly string $metodo,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            metodo: $request->string('metodo'),
        );
    }
}
