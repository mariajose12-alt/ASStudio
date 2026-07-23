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
        Schema::create('bloqueos_estudio', function (Blueprint $table) {
            $table->id();
            $table->dateTime('inicio');
            $table->dateTime('fin');
            $table->string('motivo')->nullable();
            $table->foreignId('creado_por_id')->constrained('usuarios');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
