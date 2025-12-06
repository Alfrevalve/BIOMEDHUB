<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    use HasFactory;

    protected $fillable = [
        'sku',
        'nombre',
        'tipo',
        'stock_total',
        'stock_reservado',
        'descripcion',
    ];

    public function kitItems()
    {
        return $this->hasMany(ItemKitItem::class);
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    public function disponible(): int
    {
        return max(0, $this->stock_total - $this->stock_reservado);
    }

    public function reservar(int $cantidad): bool
    {
        if ($this->disponible() < $cantidad) {
            return false;
        }

        $this->increment('stock_reservado', $cantidad);
        return true;
    }

    public function consumir(int $cantidad): void
    {
        $this->decrement('stock_total', $cantidad);
        $this->stock_total = max(0, $this->stock_total);
        $this->save();
    }

    public function liberar(int $cantidad): void
    {
        $this->decrement('stock_reservado', $cantidad);
        $this->stock_reservado = max(0, $this->stock_reservado);
        $this->save();
    }
}
