<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->boolean('confirmado_por_fotografo')->default(false)->after('sueldo_neto');
            $table->timestamp('confirmado_at')->nullable()->after('confirmado_por_fotografo');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->dropColumn('confirmado_por_fotografo', 'confirmado_at');
        });
    }
};
