<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->json('material_detalle')->nullable()->after('item_kit_id');
            $table->json('equipo_detalle')->nullable()->after('material_detalle');
            $table->timestamp('entregado_en_institucion_at')->nullable()->after('fecha_entrega');
        });
    }

    public function down(): void
    {
        Schema::table('pedidos', function (Blueprint $table) {
            $table->dropColumn(['material_detalle', 'equipo_detalle', 'entregado_en_institucion_at']);
        });
    }
};
