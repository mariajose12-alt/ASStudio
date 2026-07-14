<?php

namespace App\DTOs;

use Illuminate\Http\Request;

class EmpleadoCreateDTO
{
    public function __construct(
        public readonly string  $nombre,
        public readonly string  $apellido,
        public readonly ?string $telefono,
        public readonly string  $email,
        public readonly string  $rol,
        public readonly string  $estado,
        public readonly ?string $experiencia_laboral,
        public readonly array   $certificaciones,
        public readonly ?string $password = null,
        public readonly ?float $salario_base = null,
        public readonly int $dependientes_adicionales = 0,
    ) {}

    public static function fromRequest(Request $request): self
    {
        return new self(
            nombre:               $request->nombre,
            apellido:             $request->apellido,
            telefono:             $request->telefono,
            email:                $request->email,
            rol:                  $request->rol,
            estado:               $request->estado,
            experiencia_laboral:  $request->experiencia_laboral,
            certificaciones:      array_filter(
                array_map('trim',
                    explode(',', $request->input('certificaciones', ''))
                )
            ),
            password:             $request->password,
            salario_base:         $request->tipo_salario === 'personalizado'
                ? (float) $request->salario_base
                : null,
            dependientes_adicionales: (int) $request->input('dependientes_adicionales', 0),
        );
    }
}
