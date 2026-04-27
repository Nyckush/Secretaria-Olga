<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('docentes', function (Blueprint $table) {
            $table->id();

            // Datos personales
            $table->string('nombre', 100)->nullable();
            $table->string('apellido', 100);
            $table->string('email', 100)->nullable()->unique();
            $table->string('telefono', 20)->nullable();
            $table->string('direccion', 255)->nullable();
            $table->string('dni', 8)->unique();
            $table->string('cuil', 11)->unique();

            // Nuevos campos laborales
            $table->string('legajo_junta', 50)->nullable();

            $table->boolean('cobra_asignaciones_familiares')->default(false);
            $table->boolean('trabaja_otras_instituciones')->default(false);
            $table->string('otras_instituciones')->nullable();

            $table->boolean('tiene_abono_docente')->default(false);

            $table->unsignedTinyInteger('antiguedad_institucion')->nullable();
            $table->unsignedTinyInteger('antiguedad_docente')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('docentes');
    }
};