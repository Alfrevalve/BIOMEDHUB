<?php

namespace App\Models;

use App\Actions\CrearPedidoDesdeCirugia;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Cirugia extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'institucion_id',
        'nombre',
        'fecha_programada',
        'estado',
        'cirujano_principal',
        'instrumentista_asignado',
        'instrumentista_id',
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

    public function instrumentista()
    {
        return $this->belongsTo(\App\Models\User::class, 'instrumentista_id');
    }

    public function reportes()
    {
        return $this->hasMany(\App\Models\CirugiaReporte::class);
    }

    protected static function booted(): void
    {
        static::saving(function (Cirugia $cirugia) {
            if ($cirugia->instrumentista && $cirugia->isDirty('instrumentista_id')) {
                $cirugia->instrumentista_asignado = $cirugia->instrumentista->name;
            }
        });
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

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('cirugia');
    }
}
