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
        Schema::create('instituciones', function (Blueprint $table) {
            $table->id();
            $table->string('nombre')->index();
            $table->enum('tipo', ['Publica','Privada','Militar','ONG'])->default('Publica');
            $table->string('ciudad')->nullable();
            $table->string('direccion')->nullable();
            $table->string('contacto')->nullable();   // nombre o email/teléfono
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('instituciones');
    }
};
