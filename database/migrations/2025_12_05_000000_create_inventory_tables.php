<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('items', function (Blueprint $table) {
            $table->id();
            $table->string('sku')->unique();
            $table->string('nombre');
            $table->string('tipo')->nullable();
            $table->unsignedInteger('stock_total')->default(0);
            $table->unsignedInteger('stock_reservado')->default(0);
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('item_kits', function (Blueprint $table) {
            $table->id();
            $table->string('nombre');
            $table->string('codigo')->unique();
            $table->text('descripcion')->nullable();
            $table->timestamps();
        });

        Schema::create('item_kit_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_kit_id')->constrained('item_kits')->cascadeOnDelete();
            $table->foreignId('item_id')->constrained('items')->cascadeOnDelete();
            $table->unsignedInteger('cantidad')->default(1);
            $table->timestamps();
        });

        Schema::create('reservas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('item_id')->constrained('items')->cascadeOnUpdate()->restrictOnDelete();
            $table->foreignId('pedido_id')->nullable()->constrained('pedidos')->nullOnDelete();
            $table->foreignId('cirugia_id')->nullable()->constrained('cirugias')->nullOnDelete();
            $table->unsignedInteger('cantidad');
            $table->enum('estado', ['Reservado', 'Consumido', 'Devuelto', 'Cancelado'])->default('Reservado');
            $table->text('notas')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reservas');
        Schema::dropIfExists('item_kit_items');
        Schema::dropIfExists('item_kits');
        Schema::dropIfExists('items');
    }
};
