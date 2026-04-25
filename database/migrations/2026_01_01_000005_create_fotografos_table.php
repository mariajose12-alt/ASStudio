<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('fotografos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('empleado_id')->unique()->constrained('empleados')->cascadeOnDelete();
            $table->text('certificaciones')->nullable();       // texto libre o JSON
            $table->unsignedInteger('experiencia_laboral')->default(0); // años
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('fotografos');
    }
};
