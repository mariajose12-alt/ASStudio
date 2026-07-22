<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {

            $table->decimal('incentivo_ventas', 12, 2)
                ->default(0)
                ->after('regalia_pascual');

        });
    }

    public function down(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {

            $table->dropColumn('incentivo_ventas');

        });
    }
};
