<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE participaciones_sesion DROP COLUMN estado_participacion");
        DB::statement("ALTER TABLE participaciones_sesion ADD COLUMN estado_participacion VARCHAR(20) DEFAULT 'PENDIENTE'");
    }
};
