<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Prediccion extends Model
{
    use HasFactory;

    protected $table = 'predicciones';

    protected $fillable = [
        'servicio_id',
        'ciudad_id',
        'zona',
        'tipo_prediccion',
        'probabilidad',
        'descripcion',
        'factores',
        'fecha_prediccion',
        'alerta_enviada',
        'se_cumplio',
    ];

    protected $casts = [
        'factores' => 'array',
        'probabilidad' => 'decimal:2',
        'fecha_prediccion' => 'datetime',
        'alerta_enviada' => 'boolean',
        'se_cumplio' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relación con Servicio
     */
    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    /**
     * Relación con Ciudad
     */
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    /**
     * Scope para predicciones pendientes de enviar alerta
     */
    public function scopePendientesAlerta($query)
    {
        return $query->where('alerta_enviada', false)
                    ->where('fecha_prediccion', '>', now())
                    ->where('probabilidad', '>=', 70);
    }

    /**
     * Scope para predicciones activas (futuras)
     */
    public function scopeActivas($query)
    {
        return $query->where('fecha_prediccion', '>', now());
    }

    /**
     * Scope para predicciones pasadas
     */
    public function scopePasadas($query)
    {
        return $query->where('fecha_prediccion', '<=', now());
    }

    /**
     * Obtener nivel de riesgo
     */
    public function getNivelRiesgoAttribute()
    {
        if ($this->probabilidad >= 80) return 'Muy Alto';
        if ($this->probabilidad >= 60) return 'Alto';
        if ($this->probabilidad >= 40) return 'Medio';
        if ($this->probabilidad >= 20) return 'Bajo';
        return 'Muy Bajo';
    }

    /**
     * Obtener color según probabilidad
     */
    public function getColorAttribute()
    {
        if ($this->probabilidad >= 80) return 'danger';
        if ($this->probabilidad >= 60) return 'warning';
        if ($this->probabilidad >= 40) return 'info';
        return 'success';
    }

    /**
     * Marcar alerta como enviada
     */
    public function marcarAlertaEnviada()
    {
        $this->alerta_enviada = true;
        $this->save();
    }

    /**
     * Verificar si se cumplió la predicción
     */
    public function verificarCumplimiento()
    {
        // Buscar reportes en la zona y fecha predicha
        $reportes = Reporte::where('servicio_id', $this->servicio_id)
            ->whereDate('created_at', $this->fecha_prediccion->format('Y-m-d'))
            ->when($this->ciudad_id, function($q) {
                $q->where('ciudad_id', $this->ciudad_id);
            })
            ->when($this->zona, function($q) {
                $q->where(function($query) {
                    $query->where('barrio', 'LIKE', '%' . $this->zona . '%')
                          ->orWhere('localidad', 'LIKE', '%' . $this->zona . '%');
                });
            })
            ->count();

        // Si hay 3+ reportes, consideramos que se cumplió
        $this->se_cumplio = $reportes >= 3;
        $this->save();

        return $this->se_cumplio;
    }
}
