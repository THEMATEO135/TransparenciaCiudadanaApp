<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReporteUpdate extends Model
{
    use HasFactory;

    protected $table = 'reporte_updates';

    protected $fillable = [
        'reporte_id',
        'user_id',
        'tipo',
        'contenido',
        'archivo_url',
        'visible_ciudadano',
        'estado_anterior',
        'estado_nuevo',
    ];

    protected $casts = [
        'visible_ciudadano' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Reporte
     */
    public function reporte()
    {
        return $this->belongsTo(Reporte::class);
    }

    /**
     * Relación con User (admin/operador que creó la actualización)
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para obtener solo actualizaciones visibles para ciudadanos
     */
    public function scopeVisibleCiudadano($query)
    {
        return $query->where('visible_ciudadano', true);
    }

    /**
     * Scope para obtener actualizaciones por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Obtener el nombre del usuario que creó la actualización
     */
    public function getNombreUsuarioAttribute()
    {
        return $this->user ? $this->user->name : 'Sistema';
    }

    /**
     * Obtener el ícono según el tipo de actualización
     */
    public function getIconoAttribute()
    {
        return match($this->tipo) {
            'comentario' => 'fa-comment',
            'cambio_estado' => 'fa-sync',
            'imagen' => 'fa-image',
            'asignacion' => 'fa-user-check',
            'reasignacion' => 'fa-exchange-alt',
            'cambio_prioridad' => 'fa-exclamation-circle',
            'actualizacion' => 'fa-pen',
            'sistema' => 'fa-cog',
            default => 'fa-info-circle'
        };
    }

    /**
     * Obtener el color según el tipo
     */
    public function getColorAttribute()
    {
        return match($this->tipo) {
            'comentario' => 'blue',
            'cambio_estado' => 'green',
            'imagen' => 'purple',
            'asignacion' => 'orange',
            'reasignacion' => 'yellow',
            'cambio_prioridad' => 'orange',
            'actualizacion' => 'teal',
            'sistema' => 'gray',
            default => 'blue'
        };
    }
}
