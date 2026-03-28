<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('nodos', function (Blueprint $table) {
            $table->id();
            $table->string('url', 255)->unique();
            $table->timestamp('registrado_en')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('nodos');
    }
};
