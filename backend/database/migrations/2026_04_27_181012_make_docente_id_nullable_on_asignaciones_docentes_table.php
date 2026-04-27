<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('asignaciones_docentes', function (Blueprint $table): void {
            $table->dropForeign(['docente_id']);
        });

        Schema::table('asignaciones_docentes', function (Blueprint $table): void {
            $table->foreignId('docente_id')->nullable()->change();
            $table->foreign('docente_id')->references('id')->on('docentes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('asignaciones_docentes', function (Blueprint $table): void {
            $table->dropForeign(['docente_id']);
        });

        Schema::table('asignaciones_docentes', function (Blueprint $table): void {
            $table->foreignId('docente_id')->nullable(false)->change();
            $table->foreign('docente_id')->references('id')->on('docentes')->cascadeOnDelete();
        });
    }
};
