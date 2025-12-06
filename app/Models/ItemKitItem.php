<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ItemKitItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'item_kit_id',
        'item_id',
        'cantidad',
    ];

    public function kit()
    {
        return $this->belongsTo(ItemKit::class, 'item_kit_id');
    }

    public function item()
    {
        return $this->belongsTo(Item::class);
    }
}
