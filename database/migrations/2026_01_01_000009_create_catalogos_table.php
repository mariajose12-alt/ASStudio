<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('catalogos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('creador_id')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->date('fecha_inicio_vigencia')->nullable();
            $table->date('fecha_fin_vigencia')->nullable();
            $table->timestamps();
        });

        Schema::create('catalogo_paquete', function (Blueprint $table) {
            $table->id();
            $table->foreignId('catalogo_id')->constrained('catalogos')->onDelete('cascade');
            $table->foreignId('paquete_id')->constrained('paquetes_fotograficos')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('catalogos');
    }
};

