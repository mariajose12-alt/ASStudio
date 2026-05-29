<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('agendas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fotografo_id')->constrained('fotografos')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->text('descripcion')->nullable();
            $table->timestamps();

            // Índice para acelerar las consultas de solapamiento
            $table->index(['fotografo_id', 'fecha_inicio', 'fecha_fin']);
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('agendas');
    }
};
