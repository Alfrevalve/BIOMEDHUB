<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class Pedido extends Model
{
    use HasFactory;

    protected $fillable = [
        'cirugia_id',
        'codigo_pedido',
        'fecha',
        'fecha_entrega',
        'estado',
        'prioridad',
        'entrega_a',
        'responsable',
    ];

    protected $casts = [
        'fecha'         => 'date',
        'fecha_entrega' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pedido $pedido) {
            if (empty($pedido->codigo_pedido)) {
                $pedido->codigo_pedido = self::generateCode();
            }
        });
    }

    public static function generateCode(): string
    {
        return 'PD-' . now('America/Lima')->format('Ymd-Hi') . '-' . Str::upper(Str::random(4));
    }

    /** Relaciones */
    public function cirugia(): BelongsTo
    {
        return $this->belongsTo(Cirugia::class);
    }

    /** Scopes de tablero logístico */
    public function scopeNoCerrados($q)
    {
        return $q->whereNotIn('estado', ['Entregado', 'Anulado']);
    }

    public function scopeParaEntrega($q, $date)
    {
        return $q->whereDate('fecha_entrega', $date);
    }
}
