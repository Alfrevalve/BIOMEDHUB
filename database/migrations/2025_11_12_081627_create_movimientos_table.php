<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('movimientos', function (Blueprint $table) {
            $table->id();

            $table->foreignId('equipo_id')->constrained('equipos')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('institucion_id')->nullable()->constrained('instituciones')->nullOnDelete();
            $table->foreignId('cirugia_id')->nullable()->constrained('cirugias')->nullOnDelete();

            $table->string('nombre'); // autogenerado: "EQ Rojo -> INSN San Borja (12/11)"
            $table->dateTime('fecha_salida');
            $table->dateTime('fecha_retorno')->nullable();

            $table->enum('estado_mov', ['Programado','En uso','Devuelto','Observado'])->default('Programado');
            $table->enum('motivo', ['Cirugia','Prestamo','Consignacion','Mantenimiento','Demostracion'])->default('Cirugia');
            $table->enum('servicio', ['Neuro','Columna','Maxilofacial','OTORRINO','Otro'])->default('Neuro');

            $table->json('material_enviado')->nullable(); // ["Fresa","Adaptador","Tubo"]
            $table->string('entregado_por')->nullable();
            $table->string('recibido_por')->nullable();
            $table->string('documento_soporte')->nullable(); // guia/OC

            $table->timestamps();

            $table->index(['fecha_salida','estado_mov']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('movimientos');
    }
};
