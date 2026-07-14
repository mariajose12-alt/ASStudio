<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Dependientes ADICIONALES (no confundir con el núcleo familiar directo:
     * cónyuge/concubino e hijos menores, que ya están cubiertos sin costo
     * extra por el 3.04% de SFS). Solo cuentan aquí los dependientes que el
     * trabajador registra fuera del núcleo directo (ej. padres) y por los
     * cuales la TSS cobra una cápita adicional (Resolución 624-02 CNSS).
     */
    public function up(): void
    {
        Schema::table('fotografos', function (Blueprint $table) {
            $table->unsignedTinyInteger('dependientes_adicionales')
                ->default(0)
                ->after('salario_base');
        });
    }

    public function down(): void
    {
        Schema::table('fotografos', function (Blueprint $table) {
            $table->dropColumn('dependientes_adicionales');
        });
    }
};
