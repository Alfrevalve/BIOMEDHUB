<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cirugias', function (Blueprint $table) {
            $table->foreignId('instrumentista_id')
                ->nullable()
                ->after('cirujano_principal')
                ->constrained('users')
                ->nullOnDelete();
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->timestamp('listo_despacho_at')->nullable()->after('fecha_entrega');
            $table->string('transportista')->nullable()->after('prioridad');
            $table->string('transportista_contacto')->nullable()->after('transportista');
        });

        Schema::table('movimientos', function (Blueprint $table) {
            $table->foreignId('pedido_id')
                ->nullable()
                ->after('cirugia_id')
                ->constrained('pedidos')
                ->nullOnDelete();
            $table->string('transportista')->nullable()->after('servicio');
            $table->string('transportista_contacto')->nullable()->after('transportista');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropConstrainedForeignId('pedido_id');
            $table->dropColumn(['transportista', 'transportista_contacto']);
        });

        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['listo_despacho_at', 'transportista', 'transportista_contacto']);
        });

        Schema::table('cirugias', function (Blueprint $table) {
            $table->dropConstrainedForeignId('instrumentista_id');
        });
    }
};
