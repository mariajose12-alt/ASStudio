<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("ALTER TABLE empleados DROP CONSTRAINT empleados_rol_check");
        DB::statement("ALTER TABLE empleados ADD CONSTRAINT empleados_rol_check CHECK (rol IN ('FOTOGRAFO', 'ADMINISTRADOR', 'SOCIO_ESTUDIO'))");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE empleados DROP CONSTRAINT empleados_rol_check");
        DB::statement("ALTER TABLE empleados ADD CONSTRAINT empleados_rol_check CHECK (rol IN ('FOTOGRAFO', 'ADMINISTRADOR'))");
    }
};
