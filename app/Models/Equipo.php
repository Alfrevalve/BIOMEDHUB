<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Equipo extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'nombre','codigo_interno','tipo','estado_actual','institucion_id',
        'marca_modelo','serie','responsable_actual','observaciones'
    ];

    public function institucion()
    {
        return $this->belongsTo(Institucion::class);
    }

    public function movimientos()
    {
        return $this->hasMany(Movimiento::class);
    }

    public function cirugias()
    {
        return $this->belongsToMany(Cirugia::class, 'movimientos')->withTimestamps();
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('equipo');
    }
}
