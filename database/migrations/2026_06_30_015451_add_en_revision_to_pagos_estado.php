<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement('ALTER TABLE public.pagos DROP CONSTRAINT pagos_estado_check');

        DB::statement("
            ALTER TABLE public.pagos ADD CONSTRAINT pagos_estado_check
            CHECK (((estado)::text = ANY ((ARRAY[
                'PENDIENTE'::character varying,
                'CONFIRMADO'::character varying,
                'RECHAZADO'::character varying,
                'EN_REVISION'::character varying
            ])::text[])))
        ");
    }

    public function down(): void
    {
        DB::statement('ALTER TABLE public.pagos DROP CONSTRAINT pagos_estado_check');

        DB::statement("
            ALTER TABLE public.pagos ADD CONSTRAINT pagos_estado_check
            CHECK (((estado)::text = ANY ((ARRAY[
                'PENDIENTE'::character varying,
                'CONFIRMADO'::character varying,
                'RECHAZADO'::character varying
            ])::text[])))
        ");
    }
};
