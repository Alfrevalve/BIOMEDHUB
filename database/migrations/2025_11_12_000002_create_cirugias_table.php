<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('cirugias', function (Blueprint $table) {
            $table->id();
            $table->foreignId('institucion_id')->constrained('instituciones')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('nombre');                       // Nombre de la cirugia
            $table->timestamp('fecha_programada');          // datetime con zona horaria del servidor
            $table->enum('estado', ['Pendiente','En curso','Cerrada','Reprogramada','Cancelada'])->default('Pendiente');
            $table->string('cirujano_principal')->nullable();
            $table->string('instrumentista_asignado')->nullable();
            $table->enum('tipo', ['Craneo','Columna','Tumor','Pediatrica','Otro'])->default('Craneo');

            $table->boolean('crear_pedido_auto')->default(true);
            $table->string('paciente_codigo')->nullable();
            $table->decimal('monto_soles', 12, 2)->nullable();

            $table->timestamps();

            $table->index(['fecha_programada','estado']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cirugias');
    }
};
