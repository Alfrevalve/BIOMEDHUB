<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->json('material_usado')->nullable()->after('material_enviado');
            $table->timestamp('recogida_solicitada_at')->nullable()->after('material_usado');
        });
    }

    public function down(): void
    {
        Schema::table('movimientos', function (Blueprint $table) {
            $table->dropColumn(['material_usado', 'recogida_solicitada_at']);
        });
    }
};
