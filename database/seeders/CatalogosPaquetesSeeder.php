<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Catalogo;
use App\Models\PaqueteFotografico;
use App\Models\Usuario;
use Carbon\Carbon;

class CatalogosPaquetesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscamos un administrador para asignarlo como creador del catálogo
        $admin = Usuario::whereHas('empleado', function ($query) {
            $query->where('rol', 'ADMINISTRADOR');
        })->first();

        // 1. Crear un Catálogo Vigente
        $catalogo = Catalogo::create([
            'creador_id' => $admin ? $admin->id : null,
            'nombre' => 'Catálogo General ' . Carbon::now()->year,
            'descripcion' => 'Nuestro catálogo principal con los mejores paquetes para tus eventos.',
            'activo' => true,
            'fecha_inicio_vigencia' => Carbon::now()->startOfYear(),
            'fecha_fin_vigencia' => Carbon::now()->endOfYear(),
        ]);

        // 2. Crear Paquetes Fotográficos
        $paqueteBasico = PaqueteFotografico::create([
            'nombre' => 'Sesión Básica en Estudio',
            'descripcion' => 'Sesión de 45 minutos. Ideal para retratos personales o CV.',
            'precio_base' => 50.00,
            'cantidad_fotos_incluidas' => 10,
            'activo' => true,
        ]);

        $paquetePremium = PaqueteFotografico::create([
            'nombre' => 'Sesión Premium Exterior',
            'descripcion' => 'Sesión de 2 horas en locación exterior. Incluye cambios de vestuario.',
            'precio_base' => 150.00,
            'cantidad_fotos_incluidas' => 30,
            'activo' => true,
        ]);

        $paqueteBoda = PaqueteFotografico::create([
            'nombre' => 'Cobertura de Boda',
            'descripcion' => 'Cobertura completa del evento (hasta 8 horas).',
            'precio_base' => 800.00,
            'cantidad_fotos_incluidas' => 150,
            'activo' => true,
        ]);

        // 3. Asociar los paquetes al catálogo mediante la tabla pivote
        $catalogo->paquetes()->attach([
            $paqueteBasico->id,
            $paquetePremium->id,
            $paqueteBoda->id,
        ]);
    }
}
