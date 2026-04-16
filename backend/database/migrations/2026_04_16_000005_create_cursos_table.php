<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cursos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('anexo_id')->constrained('anexos')->cascadeOnDelete();
            $table->string('nombre', 100);
            $table->string('division', 10);
            $table->enum('turno', ['Mañana', 'Tarde', 'Noche']);
            $table->year('ciclo_lectivo');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cursos');
    }
};
