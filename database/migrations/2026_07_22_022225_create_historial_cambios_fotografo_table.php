<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_cambios_fotografo', function (Blueprint $table) {
            $table->id();
            $table->foreignId('fotografo_id')->constrained('fotografos')->cascadeOnDelete();
            $table->string('campo'); // 'salario_base' o 'dependientes_adicionales'
            $table->string('valor_anterior')->nullable();
            $table->string('valor_nuevo')->nullable();
            $table->foreignId('cambiado_por_id')->nullable()->constrained('administradores')->nullOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_cambios_fotografo');
    }
};
