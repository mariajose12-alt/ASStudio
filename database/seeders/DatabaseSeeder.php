<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Llamamos a los seeders modulares para mantener el orden
        $this->call([
            UsuariosSeeder::class,
            CatalogosPaquetesSeeder::class,
            HorarioSeeder::class,
            ConfiguracionNominaSeeder::class,
        ]);
    }
}
