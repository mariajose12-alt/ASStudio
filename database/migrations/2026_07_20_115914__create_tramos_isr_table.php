<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Escala progresiva del ISR (DGII), versionada por fecha de vigencia.
     * Reemplaza las constantes ISR_* hardcodeadas en NominaService, mismo
     * criterio que parametros_nomina: un ajuste anual de la DGII no debe
     * requerir un deploy, y las nóminas de meses pasados deben poder
     * recalcularse con la escala que estaba vigente en ese momento.
     */
    public function up(): void
    {
        Schema::create('tramos_isr', function (Blueprint $table) {
            $table->id();
            $table->unsignedTinyInteger('orden'); // 1, 2, 3... para ordenar la evaluación
            $table->decimal('desde_anual', 14, 2);
            $table->decimal('hasta_anual', 14, 2)->nullable(); // null = último tramo, sin techo
            $table->decimal('tasa', 5, 4); // 0.15, 0.20, 0.25...
            $table->decimal('monto_fijo_adicional', 14, 2)->default(0); // el "+ RD$31,216" de la escala DGII
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->string('fuente')->nullable();
            $table->timestamps();

            $table->index(['vigente_desde', 'orden']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tramos_isr');
    }
};
