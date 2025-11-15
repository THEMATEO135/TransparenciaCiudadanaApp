<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Estado extends Model
{
    protected $table = 'estados';

    protected $fillable = [
        'nombre',
        'etiqueta',
        'color',
        'icono',
        'es_estado_final',
        'orden',
        'activo',
        'descripcion',
    ];

    protected $casts = [
        'es_estado_final' => 'boolean',
        'activo' => 'boolean',
        'orden' => 'integer',
    ];

    /**
     * Relación con reportes
     */
    public function reportes()
    {
        return $this->hasMany(Reporte::class, 'estado_id');
    }

    /**
     * Scope para obtener solo estados activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para ordenar por orden
     */
    public function scopeOrdenados($query)
    {
        return $query->orderBy('orden');
    }

    /**
     * Obtener el badge HTML para mostrar el estado
     */
    public function getBadgeAttribute()
    {
        return sprintf(
            '<span class="badge" style="background-color: %s; color: white;">%s %s</span>',
            $this->color,
            $this->icono,
            $this->etiqueta
        );
    }
}
