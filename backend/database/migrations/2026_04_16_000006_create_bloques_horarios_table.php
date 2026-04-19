<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bloques_horarios', function (Blueprint $table) {
            $table->tinyIncrements('id');
            $table->unsignedTinyInteger('orden')->unique();
            $table->time('hora_inicio');
            $table->time('hora_fin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bloques_horarios');
    }
};
