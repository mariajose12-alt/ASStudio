<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('reembolsos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('pago_original_id')->constrained('pagos')->cascadeOnDelete();
            $table->date('fecha_procesado')->nullable();
            $table->decimal('monto', 12, 2);
            $table->text('motivo')->nullable();
            $table->enum('estado', ['PENDIENTE', 'PROCESADO', 'RECHAZADO'])->default('PENDIENTE');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reembolsos');
    }
};
