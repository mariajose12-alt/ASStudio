<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('fotografos', function (Blueprint $table) {
            $table->decimal('salario_base', 10, 2)->nullable()->after('certificaciones');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('fotografos', function (Blueprint $table) {
            $table->dropColumn('salario_base');
        });
    }
};
