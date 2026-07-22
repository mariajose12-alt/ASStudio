<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->index('cliente_id');
            $table->index('fotografo_id');
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->index('reserva_id');
            $table->index('cliente_id');
        });

        Schema::table('fotografias', function (Blueprint $table) {
            $table->index('sesion_id');
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->index('usuario_id');
        });

        Schema::table('solicitudes_ayudantes', function (Blueprint $table) {
            $table->index('sesion_id');
            $table->index('fotografo_solicitante_id');
        });

        Schema::table('nominas', function (Blueprint $table) {
            $table->index('creada_por_id');
        });
    }

    public function down(): void
    {
        Schema::table('reservas', function (Blueprint $table) {
            $table->dropIndex(['cliente_id']);
            $table->dropIndex(['fotografo_id']);
        });

        Schema::table('pagos', function (Blueprint $table) {
            $table->dropIndex(['reserva_id']);
            $table->dropIndex(['cliente_id']);
        });

        Schema::table('fotografias', function (Blueprint $table) {
            $table->dropIndex(['sesion_id']);
        });

        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropIndex(['usuario_id']);
        });

        Schema::table('solicitudes_ayudantes', function (Blueprint $table) {
            $table->dropIndex(['sesion_id']);
            $table->dropIndex(['fotografo_solicitante_id']);
        });

        Schema::table('nominas', function (Blueprint $table) {
            $table->dropIndex(['creada_por_id']);
        });
    }
};
