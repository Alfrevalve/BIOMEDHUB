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
        Schema::table('instituciones', function (Blueprint $table) {
            $table->string('codigo_unico')->nullable()->after('nombre');
            $table->string('nombre_establecimiento')->nullable()->after('codigo_unico');
            $table->string('clasificacion')->nullable()->after('nombre_establecimiento');
            $table->string('departamento')->nullable()->after('tipo');
            $table->string('provincia')->nullable()->after('departamento');
            $table->string('distrito')->nullable()->after('provincia');
            $table->string('ubigeo', 10)->nullable()->after('distrito');
            $table->string('codigo_disa')->nullable()->after('contacto');
            $table->string('codigo_red')->nullable()->after('codigo_disa');
            $table->string('codigo_microrred')->nullable()->after('codigo_red');
            $table->string('disa')->nullable()->after('codigo_microrred');
            $table->string('red')->nullable()->after('disa');
            $table->string('microrred')->nullable()->after('red');
            $table->string('codigo_ue')->nullable()->after('microrred');
            $table->string('unidad_ejecutora')->nullable()->after('codigo_ue');
            $table->string('categoria')->nullable()->after('unidad_ejecutora');
            $table->string('telefono')->nullable()->after('categoria');
            $table->string('tipo_doc_categorizacion')->nullable()->after('telefono');
            $table->string('nro_doc_categorizacion')->nullable()->after('tipo_doc_categorizacion');
            $table->string('horario')->nullable()->after('nro_doc_categorizacion');
            $table->date('inicio_actividad')->nullable()->after('horario');
            $table->string('director_medico')->nullable()->after('inicio_actividad');
            $table->string('estado_institucion')->nullable()->after('director_medico');
            $table->string('situacion')->nullable()->after('estado_institucion');
            $table->string('condicion')->nullable()->after('situacion');
            $table->string('inspeccion')->nullable()->after('condicion');
            $table->decimal('norte', 12, 4)->nullable()->after('inspeccion');
            $table->decimal('este', 12, 4)->nullable()->after('norte');
            $table->decimal('cota', 10, 2)->nullable()->after('este');
            $table->integer('camas')->nullable()->after('cota');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('instituciones', function (Blueprint $table) {
            $table->dropColumn([
                'codigo_unico',
                'nombre_establecimiento',
                'clasificacion',
                'departamento',
                'provincia',
                'distrito',
                'ubigeo',
                'codigo_disa',
                'codigo_red',
                'codigo_microrred',
                'disa',
                'red',
                'microrred',
                'codigo_ue',
                'unidad_ejecutora',
                'categoria',
                'telefono',
                'tipo_doc_categorizacion',
                'nro_doc_categorizacion',
                'horario',
                'inicio_actividad',
                'director_medico',
                'estado_institucion',
                'situacion',
                'condicion',
                'inspeccion',
                'norte',
                'este',
                'cota',
                'camas',
            ]);
        });
    }
};
