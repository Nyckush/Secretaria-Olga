<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docente_titulo', function (Blueprint $table) {
            $table->id();

            $table->foreignId('docente_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('titulo_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->unique(['docente_id', 'titulo_id']); // evita duplicados
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docente_titulo');
    }
};