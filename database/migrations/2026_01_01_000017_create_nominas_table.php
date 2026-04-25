<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nominas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creada_por_id')->nullable()->constrained('administradores')->nullOnDelete();
            $table->string('periodo', 50);
            $table->date('fecha_inicio');
            $table->date('fecha_fin');
            $table->decimal('total_salarios_brutos', 14, 2)->default(0);
            $table->decimal('total_descuentos_legales', 14, 2)->default(0); // SFS + AFP + ISR + riesgo laboral
            $table->decimal('total_aportes_patronales', 14, 2)->nullable();  // SFS + AFP + riesgos patronal
            $table->decimal('total_nomina_neta', 14, 2)->default(0);
            $table->enum('estado', ['PENDIENTE', 'CALCULADA', 'PAGADA', 'CERRADA'])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nominas');
    }
};
