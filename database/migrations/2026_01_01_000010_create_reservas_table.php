<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('catalogo_id')->constrained('catalogos')->restrictOnDelete();
            $table->foreignId('paquete_id')->constrained('paquetes_fotograficos')->restrictOnDelete();
            $table->foreignId('fotografo_id')->constrained('fotografos')->restrictOnDelete();
            $table->dateTime('fecha_inicio');
            $table->dateTime('fecha_fin')->nullable();
            $table->dateTime('fecha_solicitud')->useCurrent();
            $table->enum('tipo', ['ESTUDIO', 'EXTERIOR']);
            $table->string('lugar', 255)->nullable();
            $table->enum('estado', [
                'PENDIENTE',
                'MODIFICACION_PROPUESTA',
                'APROBADA',
                'RECHAZADA',
                'CANCELADA',
            ])->default('PENDIENTE');
            $table->decimal('precio_total', 12, 2);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
    }
};
