<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->string('cuenta_destino_detectada')->nullable()->after('referencia_detectada');
            $table->boolean('cuenta_destino_valida')->nullable()->after('cuenta_destino_detectada');
        });
    }

    public function down(): void
    {
        Schema::table('comprobantes', function (Blueprint $table) {
            $table->dropColumn(['cuenta_destino_detectada', 'cuenta_destino_valida']);
        });
    }
};
