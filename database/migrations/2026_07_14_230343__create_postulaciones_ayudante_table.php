<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('postulaciones_ayudante', function (Blueprint $table) {
            $table->id();
            $table->foreignId('solicitud_ayudante_id')->constrained('solicitudes_ayudantes')->cascadeOnDelete();
            $table->foreignId('fotografo_id')->constrained('fotografos')->cascadeOnDelete();
            $table->enum('estado', ['PENDIENTE', 'CONFIRMADO', 'RECHAZADO'])->default('PENDIENTE');
            $table->timestamps();

            $table->unique(['solicitud_ayudante_id', 'fotografo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('postulaciones_ayudante');
    }
};
