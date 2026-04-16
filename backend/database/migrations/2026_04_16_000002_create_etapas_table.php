<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('etapas', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 50);
            $table->tinyInteger('orden')->unsigned();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('etapas');
    }
};
