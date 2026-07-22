<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('nominas', function (Blueprint $table) {

            $table->boolean('incentivos_activos')
                ->default(false)
                ->after('fecha_fin');

            $table->decimal('total_incentivos', 14, 2)
                ->default(0)
                ->after('total_regalia_pascual');

        });
    }

    public function down(): void
    {
        Schema::table('nominas', function (Blueprint $table) {

            $table->dropColumn([
                'incentivos_activos',
                'total_incentivos'
            ]);

        });
    }
};
