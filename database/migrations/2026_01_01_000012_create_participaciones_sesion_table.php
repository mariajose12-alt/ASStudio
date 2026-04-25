<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('participaciones_sesion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->foreignId('fotografo_id')->constrained('fotografos')->cascadeOnDelete();
            $table->foreignId('agenda_id')->nullable()->constrained('agendas')->nullOnDelete(); // ← agregado según diagrama
            $table->enum('rol', ['PRINCIPAL', 'ASISTENTE']);
            $table->decimal('porcentaje_comision', 5, 2)->default(0);
            $table->boolean('estado_participacion')->default(true);
            $table->decimal('horas_trabajadas', 6, 2)->default(0);
            $table->timestamps();

            $table->unique(['sesion_id', 'fotografo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('participaciones_sesion');
    }
};
