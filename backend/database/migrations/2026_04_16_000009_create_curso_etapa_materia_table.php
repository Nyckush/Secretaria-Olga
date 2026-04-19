<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('curso_etapa_materia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_etapa_id')->constrained('curso_etapa')->cascadeOnDelete();
            $table->foreignId('curso_materia_id')->constrained('curso_materia')->cascadeOnDelete();
            $table->unsignedTinyInteger('horas_catedra')->default(0);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('curso_etapa_materia');
    }
};
