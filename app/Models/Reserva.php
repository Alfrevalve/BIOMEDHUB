<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reserva extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_id',
        'pedido_id',
        'cirugia_id',
        'cantidad',
        'estado',
        'notas',
    ];

    protected $casts = [
        'cantidad' => 'integer',
    ];

    public function item()
    {
        return $this->belongsTo(Item::class);
    }

    public function pedido()
    {
        return $this->belongsTo(Pedido::class);
    }

    public function cirugia()
    {
        return $this->belongsTo(Cirugia::class);
    }
}
