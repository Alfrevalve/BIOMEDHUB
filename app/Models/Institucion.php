<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Institucion extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $table = 'instituciones';

    protected $fillable = [
        'nombre',
        'tipo',
        'ciudad',
        'direccion',
        'contacto',
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
    ];

    public function cirugias()
    {
        return $this->hasMany(\App\Models\Cirugia::class, 'institucion_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('institucion');
    }
}
