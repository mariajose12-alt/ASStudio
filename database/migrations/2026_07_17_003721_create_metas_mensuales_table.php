<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('metas_mensuales', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('mes');
            $table->unsignedSmallInteger('anio');
            $table->decimal('ingresos', 12, 2)->default(0);
            $table->unsignedInteger('reservas')->default(0);
            $table->unsignedInteger('clientes_nuevos')->default(0);
            $table->timestamps();

            $table->unique(['mes', 'anio']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('metas_mensuales');
    }
};
