<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('detalles_nomina', function (Blueprint $table) {
            $table->id();
            $table->foreignId('nomina_id')->constrained('nominas')->cascadeOnDelete();
            $table->foreignId('fotografo_id')->constrained('fotografos')->cascadeOnDelete();
            $table->decimal('salario_bruto', 12, 2)->default(0);
            $table->decimal('descuentos_legales', 12, 2)->default(0);
            $table->decimal('sueldo_neto', 12, 2)->default(0);
            $table->timestamps();

            $table->unique(['nomina_id', 'fotografo_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('detalles_nomina');
    }
};
