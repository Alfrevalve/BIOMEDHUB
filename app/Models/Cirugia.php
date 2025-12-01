<?php

namespace App\Models;

use App\Actions\CrearPedidoDesdeCirugia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cirugia extends Model
{
    use HasFactory;

    protected $fillable = [
        'institucion_id',
        'nombre',
        'fecha_programada',
        'estado',
        'cirujano_principal',
        'instrumentista_asignado',
        'tipo',
        'crear_pedido_auto',
        'paciente_codigo',
        'monto_soles',
    ];

    protected $casts = [
        'fecha_programada'  => 'datetime',
        'crear_pedido_auto' => 'boolean',
        'monto_soles'       => 'decimal:2',
    ];

    /** Relaciones */
    public function pedidos()
    {
        return $this->hasMany(\App\Models\Pedido::class);
    }

    public function institucion()
    {
        return $this->belongsTo(\App\Models\Institucion::class);
    }

    /** Scopes operativos */
    public function scopeNoCanceladas($q)
    {
        return $q->where('estado', '!=', 'Cancelada');
    }

    public function scopeParaFecha($q, $date)
    {
        return $q->whereDate('fecha_programada', $date);
    }

    public function scopeActivas48h($q)
    {
        return $q->whereBetween('fecha_programada', [now()->subHours(24), now()->addHours(24)]);
    }
}
