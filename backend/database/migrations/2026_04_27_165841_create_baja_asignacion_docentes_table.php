<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('bajas_asignaciones_docentes', function (Blueprint $table) {
            $table->id();

            $table->foreignId('asignacion_docente_id')
                ->constrained('asignaciones_docentes')
                ->cascadeOnDelete();

            $table->text('motivo');
            $table->date('fecha_baja');

            $table->enum('tipo_baja', [
                'Renuncia',
                'Finalizacion',
                'Reemplazo',
                'Otro'
            ])->default('Otro');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('bajas_asignaciones_docentes');
    }
};