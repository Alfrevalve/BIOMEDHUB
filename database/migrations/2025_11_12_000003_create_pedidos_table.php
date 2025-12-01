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
        Schema::create('pedidos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cirugia_id')->constrained('cirugias')->cascadeOnUpdate()->restrictOnDelete();

            $table->string('codigo_pedido')->unique();      // PD-XXXX
            $table->date('fecha')->nullable();              // fecha de creación/logística
            $table->timestamp('fecha_entrega')->nullable(); // entrega planificada

            $table->enum('estado', ['Solicitado','Preparacion','Despachado','Entregado','Devuelto','Anulado','Observado'])->default('Solicitado');
            $table->enum('prioridad', ['Alta','Media','Baja'])->default('Alta');

            $table->string('entrega_a')->nullable();
            $table->string('responsable')->nullable();

            $table->timestamps();

            $table->index(['estado','fecha_entrega']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pedidos');
    }
};
