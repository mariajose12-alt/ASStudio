<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->decimal('regalia_pascual', 12, 2)->default(0)->after('sueldo_neto');
        });

        Schema::table('nominas', function (Blueprint $table) {
            $table->decimal('total_regalia_pascual', 14, 2)->default(0)->after('total_nomina_neta');
        });
    }

    public function down(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->dropColumn('regalia_pascual');
        });

        Schema::table('nominas', function (Blueprint $table) {
            $table->dropColumn('total_regalia_pascual');
        });
    }
};
