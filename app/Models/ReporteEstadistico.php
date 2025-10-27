<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class ReporteEstadistico extends Model
{
    use HasFactory;

    protected $table = 'reportes_estadisticos';

    protected $fillable = [
        'nombre',
        'frecuencia',
        'configuracion',
        'destinatarios',
        'activo',
        'ultima_ejecucion',
        'proxima_ejecucion',
    ];

    protected $casts = [
        'configuracion' => 'array',
        'destinatarios' => 'array',
        'activo' => 'boolean',
        'ultima_ejecucion' => 'datetime',
        'proxima_ejecucion' => 'datetime',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Scope para reportes activos
     */
    public function scopeActivos($query)
    {
        return $query->where('activo', true);
    }

    /**
     * Scope para reportes pendientes de ejecución
     */
    public function scopePendientes($query)
    {
        return $query->where('activo', true)
                    ->where(function($q) {
                        $q->whereNull('proxima_ejecucion')
                          ->orWhere('proxima_ejecucion', '<=', now());
                    });
    }

    /**
     * Calcular próxima ejecución según frecuencia
     */
    public function calcularProximaEjecucion()
    {
        $ahora = now();

        $proxima = match($this->frecuencia) {
            'diario' => $ahora->addDay()->startOfDay()->addHours(8), // 8 AM del día siguiente
            'semanal' => $ahora->next(Carbon::MONDAY)->startOfDay()->addHours(8), // Lunes 8 AM
            'mensual' => $ahora->addMonth()->startOfMonth()->startOfDay()->addHours(8), // Primer día del mes 8 AM
            'personalizado' => $this->calcularProximaPersonalizada(),
            default => null
        };

        $this->proxima_ejecucion = $proxima;
        $this->save();

        return $proxima;
    }

    /**
     * Calcular próxima ejecución personalizada
     */
    protected function calcularProximaPersonalizada()
    {
        // Implementar lógica personalizada según configuración
        // Por ejemplo, cada X días, días específicos de la semana, etc.
        $config = $this->configuracion;

        if (isset($config['dias_intervalo'])) {
            return now()->addDays($config['dias_intervalo'])->startOfDay()->addHours(8);
        }

        return now()->addWeek(); // Default
    }

    /**
     * Marcar como ejecutado
     */
    public function marcarEjecutado()
    {
        $this->ultima_ejecucion = now();
        $this->save();
        $this->calcularProximaEjecucion();
    }
}
