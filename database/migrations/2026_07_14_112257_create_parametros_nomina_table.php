<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Parámetros de nómina (tasas TSS, monto por dependiente adicional, topes
     * de cotización) versionados por fecha de vigencia. Reemplaza las
     * constantes hardcodeadas en NominaService para que un ajuste de la TSS
     * o la DGII no requiera un deploy, y para que las nóminas de meses
     * pasados se puedan recalcular con la tarifa que estaba vigente entonces.
     */
    public function up(): void
    {
        Schema::create('parametros_nomina', function (Blueprint $table) {
            $table->id();
            $table->string('clave'); // 'tasa_afp_empleado', 'tasa_sfs_empleado', 'monto_dependiente_adicional', 'tope_cotizacion_sfs', 'tope_cotizacion_afp', etc.
            $table->decimal('valor', 14, 4);
            $table->date('vigente_desde');
            $table->date('vigente_hasta')->nullable();
            $table->string('fuente')->nullable(); // ej. "Resolución 624-02 CNSS"
            $table->timestamps();

            $table->index(['clave', 'vigente_desde']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('parametros_nomina');
    }
};
