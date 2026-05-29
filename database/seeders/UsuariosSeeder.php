<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Cliente;

class UsuariosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Crear Administrador
        $personaAdmin = Persona::create([
            'nombre' => 'Admin',
            'apellido' => 'Principal',
            'telefono' => '1234567890',
        ]);

        $usuarioAdmin = Usuario::create([
            'persona_id' => $personaAdmin->id,
            'email' => 'admin@asstudio.com',
            'contrasena' => Hash::make('password'),
            'estado' => 'ACTIVO',
        ]);

        Empleado::create([
            'usuario_id' => $usuarioAdmin->id,
            'rol' => 'ADMINISTRADOR',
        ]);

        // 3. Crear Cliente
        $personaCliente = Persona::create([
            'nombre' => 'María',
            'apellido' => 'Cliente',
            'telefono' => '5551112222',
        ]);

        $usuarioCliente = Usuario::create([
            'persona_id' => $personaCliente->id,
            'email' => 'cliente@asstudio.com',
            'contrasena' => Hash::make('password'),
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'usuario_id' => $usuarioCliente->id,
        ]);
    }
}
