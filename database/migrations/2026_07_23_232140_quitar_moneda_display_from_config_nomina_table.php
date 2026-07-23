<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_nomina', function (Blueprint $table) {
            $table->dropColumn('moneda_display');
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_nomina', function (Blueprint $table) {
            $table->string('moneda_display')
                ->default('RD$')
                ->after('incentivos_activos');
        });
    }
};
