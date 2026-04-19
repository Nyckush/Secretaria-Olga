<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('horarios', function (Blueprint $table) {
            $table->id();
            $table->foreignId('curso_etapa_materia_id')->constrained('curso_etapa_materia')->cascadeOnDelete();
            $table->foreignId('docente_id')->constrained('docentes')->cascadeOnDelete();
            $table->unsignedTinyInteger('bloque_id');
            $table->enum('dia_semana', ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes']);
            $table->date('fecha_desde');
            $table->string('hasta', 50);
            $table->timestamps();

            $table->foreign('bloque_id')->references('id')->on('bloques_horarios')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('horarios');
    }
};
