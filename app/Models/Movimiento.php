<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Movimiento extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

    protected $fillable = [
        'equipo_id',
        'institucion_id',
        'cirugia_id',
        'pedido_id',
        'nombre',
        'fecha_salida',
        'fecha_retorno',
        'estado_mov',
        'motivo',
        'servicio',
        'material_enviado',
        'material_usado',
        'transportista',
        'transportista_contacto',
        'entregado_por',
        'recibido_por',
        'documento_soporte',
        'recogida_solicitada_at',
    ];

    protected $casts = [
        'fecha_salida'     => 'datetime',
        'fecha_retorno'    => 'datetime',
        'material_enviado' => 'array',
        'material_usado'   => 'array',
        'recogida_solicitada_at' => 'datetime',
    ];

    protected static function booted(): void
    {
        static::creating(function ($m) {
            if (empty($m->nombre)) {
                $eq = $m->equipo?->nombre ?? 'Equipo';
                $inst = $m->institucion?->nombre ?? 'Sin institucion';
                $fecha = $m->fecha_salida?->timezone('America/Lima')?->format('d/m');
                $m->nombre = $fecha
                    ? "{$eq} -> {$inst} ({$fecha})"
                    : "{$eq} -> {$inst}";
            }
        });
    }

    public function equipo()      { return $this->belongsTo(Equipo::class); }
    public function institucion() { return $this->belongsTo(Institucion::class); }
    public function cirugia()     { return $this->belongsTo(Cirugia::class); }
    public function pedido()      { return $this->belongsTo(Pedido::class); }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('movimiento');
    }
}
