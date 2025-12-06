<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Pedido extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'cirugia_id',
        'item_kit_id',
        'codigo_pedido',
        'fecha',
        'fecha_entrega',
        'listo_despacho_at',
        'entregado_en_institucion_at',
        'estado',
        'prioridad',
        'entrega_a',
        'responsable',
        'transportista',
        'transportista_contacto',
        'material_detalle',
        'equipo_detalle',
    ];

    protected $casts = [
        'fecha'         => 'date',
        'fecha_entrega' => 'datetime',
        'listo_despacho_at' => 'datetime',
        'entregado_en_institucion_at' => 'datetime',
        'material_detalle' => 'array',
        'equipo_detalle' => 'array',
    ];

    protected static function booted(): void
    {
        static::creating(function (Pedido $pedido) {
            if (empty($pedido->codigo_pedido)) {
                $pedido->codigo_pedido = self::generateCode();
            }
        });

        static::created(function (Pedido $pedido) {
            $pedido->reservarKit();
        });

        static::updated(function (Pedido $pedido) {
            if ($pedido->wasChanged('estado')) {
                if ($pedido->estado === 'Despachado') {
                    $pedido->consumirReservas();
                }

                if (in_array($pedido->estado, ['Entregado', 'Devuelto'], true)) {
                    $pedido->devolverReservas();
                }
            }
        });
    }

    public static function generateCode(): string
    {
        $base = 'PD-' . now('America/Lima')->format('Ymd-Hi');

        // Intentar hasta 5 veces para evitar colisiones en concurrencia.
        for ($i = 0; $i < 5; $i++) {
            $code = $base . '-' . Str::upper(Str::random(5));
            if (! self::where('codigo_pedido', $code)->exists()) {
                return $code;
            }
        }

        // Último recurso: timestamp único.
        return $base . '-' . Str::upper(Str::random(8));
    }

    /** Relaciones */
    public function cirugia(): BelongsTo
    {
        return $this->belongsTo(Cirugia::class);
    }

    public function itemKit()
    {
        return $this->belongsTo(ItemKit::class, 'item_kit_id');
    }

    public function reservas()
    {
        return $this->hasMany(Reserva::class);
    }

    /** Inventario */
    public function reservarKit(): void
    {
        if (! $this->itemKit) {
            return;
        }

        foreach ($this->itemKit->items as $kitItem) {
            /** @var \App\Models\Item $item */
            $item = $kitItem->item;
            if (! $item || ! $item->reservar($kitItem->cantidad)) {
                continue;
            }

            Reserva::create([
                'item_id' => $item->id,
                'pedido_id' => $this->id,
                'cirugia_id' => $this->cirugia_id,
                'cantidad' => $kitItem->cantidad,
                'estado' => 'Reservado',
            ]);
        }
    }

    public function consumirReservas(): void
    {
        foreach ($this->reservas as $reserva) {
            $item = $reserva->item;
            if ($item) {
                $item->liberar($reserva->cantidad);
                $item->consumir($reserva->cantidad);
            }
            $reserva->update(['estado' => 'Consumido']);
        }
    }

    public function devolverReservas(): void
    {
        foreach ($this->reservas as $reserva) {
            $item = $reserva->item;
            if ($item) {
                $item->liberar($reserva->cantidad);
            }
            $reserva->update(['estado' => 'Devuelto']);
        }
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('pedido');
    }
}
