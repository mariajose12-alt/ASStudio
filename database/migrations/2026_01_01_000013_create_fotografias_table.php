<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografias', function (Blueprint $table) {
            $table->id();
            $table->string('nombre_original', 260)->nullable()->after('url');
            $table->foreignId('sesion_id')->constrained('sesiones')->cascadeOnDelete();
            $table->string('url', 500);
            $table->dateTime('fecha_captura')->useCurrent();
            $table->enum('estado', ['ORIGINAL', 'PENDIENTE_EDICION','EDITADA', 'ENTREGADA', 'PUBLICADA'])->default('ORIGINAL');
            $table->boolean('seleccionada')->default(false);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografias');
    }
};
