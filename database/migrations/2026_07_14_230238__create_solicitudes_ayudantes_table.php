<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_ayudantes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->foreignId('fotografo_solicitante_id')->constrained('fotografos')->cascadeOnDelete();
            $table->unsignedTinyInteger('cantidad_ayudantes');
            $table->unsignedTinyInteger('cupos_confirmados')->default(0);
            $table->enum('estado', ['ABIERTA', 'CERRADA', 'CANCELADA'])->default('ABIERTA');
            $table->text('mensaje')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_ayudantes');
    }
};
