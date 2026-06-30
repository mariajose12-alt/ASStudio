<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CuentaBancoSeeder extends Seeder
{
    public function run(): void
    {
        $cuentas = [
            // Banco Popular
            ['titular' => 'Abraham Sanchez',            'banco' => 'Banco Popular',    'numero_cuenta' => '798253084',          'tipo' => 'ahorros',   'moneda' => 'DOP', 'orden' => 1],
            ['titular' => 'Abraham Sanchez',            'banco' => 'Banco Popular',    'numero_cuenta' => '834696064',          'tipo' => 'ahorros',   'moneda' => 'USD', 'orden' => 2],
            // Banco Santa Cruz
            ['titular' => 'Abraham Sanchez',            'banco' => 'Banco Santa Cruz', 'numero_cuenta' => '11002060002119',     'tipo' => 'ahorros',   'moneda' => 'DOP', 'orden' => 3],
            ['titular' => 'Abraham Isaac Sanchez Garcia','banco' => 'Banco Santa Cruz', 'numero_cuenta' => '21002020029336',     'tipo' => 'ahorros',   'moneda' => 'USD', 'orden' => 4],
            // Banreservas
            ['titular' => 'Abraham Isaac Sanchez Garcia','banco' => 'Banreservas',      'numero_cuenta' => '9601232643',         'tipo' => 'ahorros',   'moneda' => 'DOP', 'orden' => 5],
            ['titular' => 'Abraham Isaac Sanchez Garcia','banco' => 'Banreservas',      'numero_cuenta' => '9607387194',         'tipo' => 'ahorros',   'moneda' => 'USD', 'orden' => 6],
            // BHD
            ['titular' => 'Abraham Sanchez',            'banco' => 'Banco BHD',        'numero_cuenta' => '27525850018',        'tipo' => 'ahorros',   'moneda' => 'DOP', 'orden' => 7],
            // Scotiabank
            ['titular' => 'Abraham Sanchez',            'banco' => 'Scotiabank',        'numero_cuenta' => '12009709612',        'tipo' => 'corriente', 'moneda' => 'DOP', 'orden' => 8],
            // Asociación Cibao
            ['titular' => 'Abraham Sanchez',            'banco' => 'Asociación Cibao', 'numero_cuenta' => '10-003-025005-2',    'tipo' => 'ahorros',   'moneda' => 'DOP', 'orden' => 9],
            // Digitales
            ['titular' => 'Abrahami98799879@outlook.com','banco' => 'PayPal',           'numero_cuenta' => 'Abrahami98799879@outlook.com', 'tipo' => 'paypal', 'moneda' => 'USD', 'orden' => 10],
            ['titular' => 'Abraham Sanchez',            'banco' => 'Binance',           'numero_cuenta' => '181019424',          'tipo' => 'binance',   'moneda' => 'USD', 'orden' => 11],
        ];

        DB::table('cuentas_banco')->insert(
            array_map(fn($c) => array_merge($c, [
                'activa'     => true,
                'created_at' => now(),
                'updated_at' => now(),
            ]), $cuentas)
        );
    }
}
