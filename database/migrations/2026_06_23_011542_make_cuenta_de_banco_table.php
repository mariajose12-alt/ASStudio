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
        Schema::create('cuentas_banco', function (Blueprint $table) {
            $table->id();
            $table->string('titular');
            $table->string('banco');
            $table->string('numero_cuenta');
            $table->string('tipo')->default('ahorros');
            $table->string('moneda')->default('DOP');
            $table->boolean('activa')->default(true);
            $table->integer('orden')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
