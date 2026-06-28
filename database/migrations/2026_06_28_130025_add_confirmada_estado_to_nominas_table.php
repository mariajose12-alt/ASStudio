<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE nominas DROP CONSTRAINT nominas_estado_check");
        DB::statement("ALTER TABLE nominas ADD CONSTRAINT nominas_estado_check CHECK (estado IN ('PENDIENTE', 'CALCULADA', 'CONFIRMADA', 'PAGADA', 'CERRADA'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE nominas DROP CONSTRAINT nominas_estado_check");
        DB::statement("ALTER TABLE nominas ADD CONSTRAINT nominas_estado_check CHECK (estado IN ('PENDIENTE', 'CALCULADA', 'PAGADA', 'CERRADA'))");
    }
};
