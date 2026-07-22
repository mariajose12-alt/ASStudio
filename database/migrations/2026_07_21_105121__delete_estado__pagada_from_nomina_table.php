<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Cualquier nómina que ya estuviera PAGADA pasa a CERRADA,
        // que ahora es el único estado final.
        DB::statement("UPDATE nominas SET estado = 'CERRADA' WHERE estado = 'PAGADA'");

        DB::statement('ALTER TABLE nominas DROP CONSTRAINT nominas_estado_check');
        DB::statement("ALTER TABLE nominas ADD CONSTRAINT nominas_estado_check CHECK (estado IN ('PENDIENTE', 'CALCULADA', 'CONFIRMADA', 'CERRADA'))");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE nominas DROP CONSTRAINT nominas_estado_check');
        DB::statement("ALTER TABLE nominas ADD CONSTRAINT nominas_estado_check CHECK (estado IN ('PENDIENTE', 'CALCULADA', 'PAGADA', 'CERRADA'))");
    }
};
