<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->string('estado_confirmacion')->default('PENDIENTE')->after('confirmado_at');
            $table->text('observacion_fotografo')->nullable()->after('estado_confirmacion');
        });

        DB::statement("ALTER TABLE detalles_nomina ADD CONSTRAINT detalles_nomina_estado_confirmacion_check CHECK (estado_confirmacion IN ('PENDIENTE', 'CONFIRMADO', 'EN_DISPUTA'))");

        //migrar datos existentes al nuevo enum
        DB::table('detalles_nomina')->where('confirmado_por_fotografo', true)
            ->update(['estado_confirmacion' => 'CONFIRMADO']);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::statement("ALTER TABLE detalles_nomina DROP CONSTRAINT detalles_nomina_estado_confirmacion_check");

        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->dropColumn(['estado_confirmacion', 'observacion_fotografo']);
        });
    }
};
