<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('comprobantes', function (Blueprint $table) {
            $table->id();

            // Archivo en R2 (mismo patrón que las fotos de sesión)
            $table->string('archivo_key'); // path/key en el bucket, no la URL pública

            // Datos extraídos por OCR (Google Cloud Vision)
            $table->decimal('monto_detectado', 12, 2)->nullable();
            $table->date('fecha_detectada')->nullable();
            $table->string('banco_detectado')->nullable();
            $table->string('referencia_detectada')->nullable(); // # de confirmación/transacción

            $table->enum('estado_ocr', ['PENDIENTE', 'PROCESADO', 'FALLIDO'])->default('PENDIENTE');
            $table->json('respuesta_ocr_raw')->nullable(); // respuesta cruda de Vision API, útil para debug

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('comprobantes');
    }
};
