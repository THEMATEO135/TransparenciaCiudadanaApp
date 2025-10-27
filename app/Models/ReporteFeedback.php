<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReporteFeedback extends Model
{
    use HasFactory;

    protected $table = 'reporte_feedbacks';

    protected $fillable = [
        'reporte_id',
        'resuelto',
        'calificacion',
        'nps',
        'comentario',
        'respondido_at',
        'token',
    ];

    protected $casts = [
        'resuelto' => 'boolean',
        'calificacion' => 'integer',
        'nps' => 'integer',
        'respondido_at' => 'datetime',
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
     * Generar token único para verificación
     */
    public static function generarToken()
    {
        return Str::random(64);
    }

    /**
     * Obtener categoría NPS
     */
    public function getCategoriaNpsAttribute()
    {
        if ($this->nps === null) return null;

        if ($this->nps >= 9) return 'Promotor';
        if ($this->nps >= 7) return 'Pasivo';
        return 'Detractor';
    }

    /**
     * Obtener estrellas como string
     */
    public function getEstrellasHtmlAttribute()
    {
        if (!$this->calificacion) return '';

        $html = '';
        for ($i = 1; $i <= 5; $i++) {
            $class = $i <= $this->calificacion ? 'fas' : 'far';
            $html .= "<i class='$class fa-star' style='color: #FFD700;'></i> ";
        }
        return $html;
    }

    /**
     * Scope para feedbacks respondidos
     */
    public function scopeRespondido($query)
    {
        return $query->whereNotNull('respondido_at');
    }

    /**
     * Scope para feedbacks pendientes
     */
    public function scopePendiente($query)
    {
        return $query->whereNull('respondido_at');
    }
}
