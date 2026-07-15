<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('fotografias', function (Blueprint $table) {
            $table->foreignId('subido_por_fotografo_id')->nullable()->after('sesion_id')
                ->constrained('fotografos')->nullOnDelete();
            $table->boolean('aprobada')->default(true)->after('seleccionada');
        });
    }

    public function down(): void
    {
        Schema::table('fotografias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('subido_por_fotografo_id');
            $table->dropColumn('aprobada');
        });
    }
};
