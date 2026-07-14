<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->unsignedTinyInteger('dependientes_adicionales_aplicados')->default(0)->after('descuento_isr');
            $table->decimal('monto_dependiente_unitario_usado', 10, 2)->default(0)->after('dependientes_adicionales_aplicados');
            $table->decimal('descuento_dependientes', 10, 2)->default(0)->after('monto_dependiente_unitario_usado');
        });
    }

    public function down(): void
    {
        Schema::table('detalles_nomina', function (Blueprint $table) {
            $table->dropColumn([
                'dependientes_adicionales_aplicados',
                'monto_dependiente_unitario_usado',
                'descuento_dependientes',
            ]);
        });
    }
};
