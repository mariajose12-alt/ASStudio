<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // Normaliza cualquier valor previo dejado por el bug de tipo (ej. "1", "0", "PENDIENTE")
        DB::table('participaciones_sesion')
            ->whereNotIn('estado_participacion', ['1', '0'])
            ->update(['estado_participacion' => '1']);

        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion DROP DEFAULT");
        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion TYPE boolean USING (estado_participacion::boolean)");
        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion SET DEFAULT true");
        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion SET NOT NULL");
    }

    public function down(): void
    {
        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion DROP DEFAULT");
        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion TYPE VARCHAR(20) USING (CASE WHEN estado_participacion THEN '1' ELSE '0' END)");
        DB::statement("ALTER TABLE participaciones_sesion ALTER COLUMN estado_participacion SET DEFAULT 'PENDIENTE'");
    }
};
