<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->index('estado');
        });

        Schema::table('reservas', function (Blueprint $table) {
            $table->index('estado');
            $table->index(['estado', 'fecha_inicio']);
        });
    }

    public function down(): void
    {
        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['estado']);
        });

        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex(['estado']);
            $table->dropIndex(['estado', 'fecha_inicio']);
        });
    }
};
