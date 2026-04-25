<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificaciones', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuarios')->cascadeOnDelete();
            $table->text('mensaje');
            $table->dateTime('fecha_envio')->useCurrent();
            $table->boolean('leida')->default(false);
            $table->enum('tipo', [
                'RESERVA_APROBADA',
                'RESERVA_RECHAZADA',
                'MODIFICACION_PROPUESTA',
                'RECORDATORIO_SESION',
                'NOMINA_GENERADA',   // ← operacional: cuando se genera
                'NOMINA_DENEGADA',   // ← corregido según diagrama
                'PAGO_RECIBIDO',
            ]);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificaciones');
    }
};
