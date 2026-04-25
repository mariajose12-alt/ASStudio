<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sesiones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->unique()->constrained('reservas')->cascadeOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin');
            $table->string('lugar', 255)->nullable();
            $table->enum('estado', [
                'CONFIRMADA',
                'EN_PROCESO',
                'EN_EDICION',
                'GALERIA_DISPONIBLE',
                'FINALIZADA',
                'CERRADA',
            ])->default('CONFIRMADA');
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sesiones');
    }
};
