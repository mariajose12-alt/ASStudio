<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('reserva_id')->constrained('reservas')->cascadeOnDelete();
            $table->foreignId('cliente_id')->constrained('clientes')->cascadeOnDelete();
            $table->foreignId('comprobante_id')->nullable()->unique()->constrained('comprobantes')->nullOnDelete();
            $table->decimal('monto', 12, 2);
            $table->dateTime('fecha_registro')->useCurrent();
            $table->dateTime('fecha_completado')->nullable();
            $table->enum('estado', ['PENDIENTE', 'CONFIRMADO', 'RECHAZADO'])->default('PENDIENTE');
            $table->enum('metodo', ['TRANSFERENCIA', 'TARJETA', 'EFECTIVO']);
            $table->enum('tipo', ['ANTICIPO', 'FINAL', 'COMPLETO']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pagos');
    }
};
