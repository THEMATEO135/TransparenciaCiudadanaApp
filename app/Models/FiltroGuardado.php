<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FiltroGuardado extends Model
{
    use HasFactory;

    protected $table = 'filtros_guardados';

    protected $fillable = [
        'user_id',
        'nombre',
        'filtros',
        'es_publico',
        'uso_count',
    ];

    protected $casts = [
        'filtros' => 'array',
        'es_publico' => 'boolean',
        'uso_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con User
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope para filtros del usuario actual
     */
    public function scopeMisFiltros($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Scope para filtros públicos
     */
    public function scopePublicos($query)
    {
        return $query->where('es_publico', true);
    }

    /**
     * Scope para filtros disponibles para un usuario
     */
    public function scopeDisponiblesPara($query, $userId)
    {
        return $query->where(function($q) use ($userId) {
            $q->where('user_id', $userId)
              ->orWhere('es_publico', true);
        });
    }

    /**
     * Incrementar contador de uso
     */
    public function incrementarUso()
    {
        $this->increment('uso_count');
    }

    /**
     * Obtener filtros como query string
     */
    public function getQueryString()
    {
        return http_build_query($this->filtros);
    }
}
