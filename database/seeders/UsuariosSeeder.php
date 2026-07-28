<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\Persona;
use App\Models\Usuario;
use App\Models\Empleado;
use App\Models\Cliente;
use App\Models\Fotografo;
use App\Models\Administrador;

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
            'email' => 'astudiophotographyy@gmail.com',
            'contrasena' => Hash::make('password'),
            'estado' => 'ACTIVO',
        ]);

        // Se crea el registro en la tabla "administradores" porque Empleado solo
        // define el rol (ADMINISTRADOR), pero la nómina necesita referenciar
        // específicamente el id de Administrador (creada_por_id), no el de
        // Empleado, Usuario o Persona.

        $empleadoAdmin = Empleado::create([
            'usuario_id' => $usuarioAdmin->id,
            'rol' => 'ADMINISTRADOR',
        ]);

        Administrador::create([
            'empleado_id' => $empleadoAdmin->id,
        ]);

        // 2. Crear Fotógrafo
        $personaFotografo = Persona::create([
            'nombre'   => 'Carlos',
            'apellido' => 'Fotógrafo',
            'telefono' => '5559998888',
        ]);

        $usuarioFotografo = Usuario::create([
            'persona_id' => $personaFotografo->id,
            'email'      => 'felipesanchezfs26@gmail.com',
            'contrasena' => Hash::make('password'),
            'estado'     => 'ACTIVO',
        ]);

        $empleadoFotografo = Empleado::create([
            'usuario_id' => $usuarioFotografo->id,
            'rol'        => 'FOTOGRAFO',
        ]);

        Fotografo::create([
            'empleado_id' => $empleadoFotografo->id,
        ]);

        // 3. Crear Cliente
        $personaCliente = Persona::create([
            'nombre' => 'María',
            'apellido' => 'Cliente',
            'telefono' => '8293338910',
        ]);

        $usuarioCliente = Usuario::create([
            'persona_id' => $personaCliente->id,
            'email' => 'maria12josecruz07@gmail.com',
            'contrasena' => Hash::make('password'),
            'estado' => 'ACTIVO',
        ]);

        Cliente::create([
            'usuario_id' => $usuarioCliente->id,
        ]);
    }
}
