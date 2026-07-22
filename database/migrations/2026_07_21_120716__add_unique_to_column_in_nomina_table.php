<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Por cada periodo duplicado, se conserva la nómina "oficial"
        // (CERRADA > CONFIRMADA > CALCULADA > PENDIENTE) y, en caso de
        // empate, la más reciente. El resto se elimina — sus detalles
        // se borran en cascada por la FK de detalles_nomina.
        DB::statement("
            DELETE FROM nominas
            WHERE id IN (
                SELECT id FROM (
                    SELECT id,
                        ROW_NUMBER() OVER (
                            PARTITION BY periodo
                            ORDER BY
                                CASE estado
                                    WHEN 'CERRADA'    THEN 4
                                    WHEN 'CONFIRMADA' THEN 3
                                    WHEN 'CALCULADA'  THEN 2
                                    WHEN 'PENDIENTE'  THEN 1
                                    ELSE 0
                                END DESC,
                                created_at DESC,
                                id DESC
                        ) AS rn
                    FROM nominas
                ) ranked
                WHERE rn > 1
            )
        ");

        Schema::table('nominas', function (Blueprint $table) {
            $table->unique('periodo');
        });
    }

    public function down(): void
    {
        Schema::table('nominas', function (Blueprint $table) {
            $table->dropUnique(['periodo']);
        });
    }
};
