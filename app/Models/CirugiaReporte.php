<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CirugiaReporte extends Model
{
    protected $fillable = [
        'cirugia_id',
        'institucion',
        'paciente',
        'hora_programada',
        'hora_inicio',
        'hora_termino',
        'consumo',
        'notas',
        'evidencia_path',
    ];

    protected $casts = [
        'hora_programada' => 'datetime',
        'hora_inicio'     => 'datetime',
        'hora_termino'    => 'datetime',
    ];

    public function cirugia(): BelongsTo
    {
        return $this->belongsTo(Cirugia::class);
    }
}
