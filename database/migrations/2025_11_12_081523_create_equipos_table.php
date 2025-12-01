<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('equipos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->index();                    // Ej: Equipo Rojo, Equipo Verde, etc.
            $table->string('codigo_interno')->nullable();
            $table->enum('tipo', ['Craneo','Columna','Motor','Consola','Fresas'])->default('Craneo');
            $table->enum('estado_actual', ['Disponible','En cirugia','Asignado','En mantenimiento','En transito'])->default('Disponible');
            $table->foreignId('institucion_id')->nullable()->constrained('instituciones')->nullOnDelete(); // ubicacion actual
            $table->string('marca_modelo')->nullable();
            $table->string('serie')->nullable();
            $table->string('responsable_actual')->nullable();
            $table->text('observaciones')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('equipos');
    }
};
