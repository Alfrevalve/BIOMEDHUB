<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Institucion extends Model
{
    use HasFactory;

    protected $table = 'instituciones';

    protected $fillable = [
        'nombre',
        'tipo',
        'ciudad',
        'direccion',
        'contacto',
    ];

    public function cirugias()
    {
        return $this->hasMany(\App\Models\Cirugia::class, 'institucion_id');
    }
}
