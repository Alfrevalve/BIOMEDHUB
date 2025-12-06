<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('cirugia_reportes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cirugia_id')->constrained('cirugias')->cascadeOnUpdate()->cascadeOnDelete();
            $table->string('institucion')->nullable();
            $table->string('paciente')->nullable();
            $table->dateTime('hora_programada')->nullable();
            $table->dateTime('hora_inicio')->nullable();
            $table->dateTime('hora_termino')->nullable();
            $table->text('consumo')->nullable();
            $table->text('notas')->nullable();
            $table->string('evidencia_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cirugia_reportes');
    }
};
