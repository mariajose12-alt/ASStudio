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
        Schema::create('horarios_fotografo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fotografo_id')->constrained('fotografos')->cascadeOnDelete();
            // 0=Domingo, 1=Lunes, ..., 6=Sábado  (igual que Carbon::dayOfWeek)
            $table->unsignedTinyInteger('dia_semana');
            $table->time('hora_inicio');
            $table->time('hora_fin');
            $table->timestamps();

            $table->unique(['fotografo_id', 'dia_semana']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('horarios_fotografo');
    }
};
