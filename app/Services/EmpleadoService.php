<?php

namespace App\Services;

use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Fotografo;
use App\Models\HorarioFotografo;
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

        // 2. Usuario con contraseña inutilizable — nadie la conoce ni la ve
        $usuario = Usuario::create([
            'persona_id' => $persona->id,
            'email'      => $dto->email,
            'contrasena' => Hash::make(Str::random(32)),
        ]);
        $usuario->estado = $dto->estado;
        $usuario->save();

        // 3. Empleado
        $empleado = Empleado::create([
            'usuario_id' => $usuario->id,
            'rol'        => $dto->rol,
        ]);

        // 4. Si es fotógrafo
        if ($dto->rol === 'FOTOGRAFO') {
            $fotografo = Fotografo::create([
                'empleado_id'         => $empleado->id,
                'experiencia_laboral' => $dto->experiencia_laboral,
                'certificaciones'     => $dto->certificaciones,
                'salario_base'        => $dto->salario_base,
                'dependientes_adicionales' => $dto->dependientes_adicionales,
            ]);

            $this->asignarHorarioPorDefecto($fotografo);
        }

        if ($dto->rol === 'ADMINISTRADOR') {
            \App\Models\Administrador::create(['empleado_id' => $empleado->id]);
        }

        // 5. Generar token y enviar correo de activación
        $tokenCrudo = \App\Models\ActivacionCuenta::generarPara($usuario);
        \Illuminate\Support\Facades\Mail::to($usuario->email)->send(
            new \App\Mail\ActivarCuentaFotografo($usuario, $tokenCrudo)
        );

        return [
            'empleado' => $empleado,
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
        ];

        if ($dto->password) {
            $usuarioData['contrasena'] = Hash::make($dto->password);
        }

        $empleado->usuario->update($usuarioData);
        $empleado->usuario->estado = $dto->estado;
        $empleado->usuario->save();
        $empleado->update(['rol' => $dto->rol]);

        if ($dto->rol === 'FOTOGRAFO') {
            $fotografo = Fotografo::updateOrCreate(
                ['empleado_id' => $empleado->id],
                [
                    'experiencia_laboral' => $dto->experiencia_laboral,
                    'certificaciones'     => $dto->certificaciones,
                    'salario_base'        => $dto->salario_base,
                    'dependientes_adicionales' => $dto->dependientes_adicionales,
                ]
            );

            // Si pasó de otro rol a FOTOGRAFO (o si por algún motivo no tenía
            // horarios todavía), le asigna el horario por defecto sin pisar
            // uno que ya existiera.
            $this->asignarHorarioPorDefecto($fotografo);
        }

        if ($dto->rol === 'ADMINISTRADOR') {
            \App\Models\Administrador::firstOrCreate(['empleado_id' => $empleado->id]);
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

    /**
     * Mismo horario que usa HorarioSeeder para el fotógrafo de ejemplo:
     * lunes a viernes 11:00-22:00, sábado 08:00-22:00, domingo cerrado.
     * No pisa horarios que ya existan (idempotente).
     */
    private function asignarHorarioPorDefecto(Fotografo $fotografo): void
    {
        $horarios = [
            ['dia_semana' => 1, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Lunes
            ['dia_semana' => 2, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Martes
            ['dia_semana' => 3, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Miércoles
            ['dia_semana' => 4, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Jueves
            ['dia_semana' => 5, 'hora_inicio' => '11:00', 'hora_fin' => '22:00'], // Viernes
            ['dia_semana' => 6, 'hora_inicio' => '08:00', 'hora_fin' => '22:00'], // Sábado
            // Domingo (0) no se inserta → fotógrafo no trabaja
        ];

        foreach ($horarios as $horario) {
            HorarioFotografo::firstOrCreate(
                [
                    'fotografo_id' => $fotografo->id,
                    'dia_semana'   => $horario['dia_semana'],
                ],
                $horario
            );
        }
    }
}
