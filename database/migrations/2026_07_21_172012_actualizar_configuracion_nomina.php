<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('configuracion_nomina', function (Blueprint $table) {

            $table->renameColumn('salario_base', 'salario_base_default');

            $table->boolean('incentivos_activos')
                ->default(false)
                ->after('salario_base_default');

            $table->string('moneda_display')
                ->default('RD$')
                ->after('incentivos_activos');

            $table->foreignId('actualizado_por_id')
                ->nullable()
                ->after('moneda_display')
                ->constrained('administradores')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('configuracion_nomina', function (Blueprint $table) {

            $table->dropConstrainedForeignId('actualizado_por_id');

            $table->dropColumn([
                'incentivos_activos',
                'moneda_display',
            ]);

            $table->renameColumn('salario_base_default', 'salario_base');
        });
    }
};
