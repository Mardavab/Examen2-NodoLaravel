<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('transacciones_pendientes', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->uuid('persona_id')->nullable();
            $table->uuid('institucion_id')->nullable();
            $table->uuid('programa_id')->nullable();
            $table->string('titulo_obtenido', 255)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('numero_cedula', 50)->nullable();
            $table->text('titulo_tesis')->nullable();
            $table->string('menciones', 100)->nullable();
            $table->string('origen_nodo', 255)->nullable();
            $table->timestamp('creado_en')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('transacciones_pendientes');
    }
};
