<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('solicitudes_estudio', function (Blueprint $table) {
            $table->id();

            // Datos del cliente
            $table->string('nombre');
            $table->string('apellido');
            $table->string('email');
            $table->string('telefono');
            $table->text('invitados')->nullable();

            // Detalles de la sesión
            $table->enum('finalidad', ['fotografia', 'video', 'podcast', 'contenido_personal', 'evento']);
            $table->unsignedTinyInteger('cantidad_personas');
            $table->boolean('color_fondo_adicional')->default(false);
            $table->string('color_fondo')->nullable();
            $table->enum('iluminacion', ['luz_fija', 'flashes', 'luz_natural', 'luz_fija_flash', 'otro']);
            $table->string('iluminacion_otro')->nullable();

            // Fecha y hora
            $table->date('fecha');
            $table->time('hora_inicio');
            $table->time('hora_fin');

            $table->enum('estado', ['pendiente', 'aprobada', 'rechazada', 'cancelada'])->default('pendiente');
            $table->foreignId('aprobada_por')->nullable()->constrained('usuarios')->nullOnDelete();
            $table->timestamp('aprobada_at')->nullable();
            $table->text('nota_socio')->nullable();

            $table->timestamps();

            $table->index('fecha');
            $table->index('estado');
            $table->index(['fecha', 'estado']); // consulta más común: disponibilidad por fecha
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('solicitudes_estudio');
    }
};
