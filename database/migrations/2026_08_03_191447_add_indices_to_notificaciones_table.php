<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            // Cubre "notificaciones del usuario, más recientes primero"
            // (el panel) y también el borrado por antigüedad/estado leída.
            $table->index(['usuario_id', 'fecha_envio']);
            $table->index(['leida', 'fecha_envio']);
        });
    }

    public function down(): void
    {
        Schema::table('notificaciones', function (Blueprint $table) {
            $table->dropIndex(['usuario_id', 'fecha_envio']);
            $table->dropIndex(['leida', 'fecha_envio']);
        });
    }
};
