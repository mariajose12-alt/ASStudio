<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Fotografo;
use App\DTOs\EmpleadoCreateDTO;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class EmpleadoService
{
    public function crear(EmpleadoCreateDTO $dto): array
    {
        // 1. Persona
        $persona = Persona::create([
            'nombre'   => $dto->nombre,
            'apellido' => $dto->apellido,
            'telefono' => $dto->telefono,
        ]);

        // 2. Password y Usuario
        $passwordPlano = Str::random(10);

        $usuario = Usuario::create([
            'persona_id' => $persona->id,
            'email'      => $dto->email,
            'contrasena' => Hash::make($passwordPlano),
            'estado'     => $dto->estado,
        ]);

        // 3. Empleado
        $empleado = Empleado::create([
            'usuario_id' => $usuario->id,
            'rol'        => $dto->rol,
        ]);

        // 4. Si es fotógrafo
        if ($dto->rol === 'FOTOGRAFO') {
            Fotografo::create([
                'empleado_id'         => $empleado->id,
                'experiencia_laboral' => $dto->experiencia_laboral,
                'certificaciones'     => $dto->certificaciones,
                'salario_base'        => $dto->salario_base,
                'dependientes_adicionales' => $dto->dependientes_adicionales,
            ]);
        }

        return [
            'empleado'         => $empleado,
            'password_inicial' => $passwordPlano,
        ];
    }

    public function actualizar(Empleado $empleado, EmpleadoCreateDTO $dto): void
    {
        $empleado->usuario->persona->update([
            'nombre'   => $dto->nombre,
            'apellido' => $dto->apellido,
            'telefono' => $dto->telefono,
        ]);

        $usuarioData = [
            'email'  => $dto->email,
            'estado' => $dto->estado,
        ];

        if ($dto->password) {
            $usuarioData['contrasena'] = Hash::make($dto->password);
        }

        $empleado->usuario->update($usuarioData);
        $empleado->update(['rol' => $dto->rol]);

        if ($dto->rol === 'FOTOGRAFO') {
            Fotografo::updateOrCreate(
                ['empleado_id' => $empleado->id],
                [
                    'experiencia_laboral' => $dto->experiencia_laboral,
                    'certificaciones'     => $dto->certificaciones,
                    'salario_base'        => $dto->salario_base,
                    'dependientes_adicionales' => $dto->dependientes_adicionales,
                ]
            );
        }
    }

    public function eliminar(Empleado $empleado): void
    {
        $usuario = $empleado->usuario;
        $persona = $usuario->persona;

        $empleado->delete();
        $usuario->delete();
        $persona->delete();
    }
}
