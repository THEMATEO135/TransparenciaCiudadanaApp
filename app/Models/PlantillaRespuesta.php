<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PlantillaRespuesta extends Model
{
    use HasFactory;

    protected $table = 'plantillas_respuesta';

    protected $fillable = [
        'nombre',
        'asunto',
        'contenido',
        'tipo',
        'activa',
        'uso_count',
    ];

    protected $casts = [
        'activa' => 'boolean',
        'uso_count' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Variables disponibles para reemplazo en plantillas
     */
    protected static $variablesDisponibles = [
        '{nombre_ciudadano}',
        '{correo}',
        '{telefono}',
        '{servicio}',
        '{ciudad}',
        '{proveedor}',
        '{barrio}',
        '{localidad}',
        '{direccion}',
        '{fecha_reporte}',
        '{fecha_estimada}',
        '{id_reporte}',
        '{estado}',
        '{descripcion}',
    ];

    /**
     * Scope para plantillas activas
     */
    public function scopeActivas($query)
    {
        return $query->where('activa', true);
    }

    /**
     * Scope por tipo
     */
    public function scopeTipo($query, $tipo)
    {
        return $query->where('tipo', $tipo);
    }

    /**
     * Procesar plantilla reemplazando variables
     */
    public function procesar($reporte)
    {
        $contenido = $this->contenido;
        $asunto = $this->asunto;

        $variables = [
            '{nombre_ciudadano}' => $reporte->nombres,
            '{correo}' => $reporte->correo,
            '{telefono}' => $reporte->telefono,
            '{servicio}' => $reporte->servicio->nombre ?? 'N/A',
            '{ciudad}' => $reporte->ciudad->nombre ?? 'N/A',
            '{proveedor}' => $reporte->proveedor->nombre ?? 'Sin especificar',
            '{barrio}' => $reporte->barrio ?? 'N/A',
            '{localidad}' => $reporte->localidad ?? 'N/A',
            '{direccion}' => $reporte->direccion ?? 'N/A',
            '{fecha_reporte}' => $reporte->created_at->format('d/m/Y H:i'),
            '{fecha_estimada}' => $reporte->deadline ? $reporte->deadline->format('d/m/Y H:i') : 'Por determinar',
            '{id_reporte}' => $reporte->id,
            '{estado}' => ucfirst(str_replace('_', ' ', $reporte->estado)),
            '{descripcion}' => $reporte->descripcion,
        ];

        foreach ($variables as $variable => $valor) {
            $contenido = str_replace($variable, $valor, $contenido);
            $asunto = str_replace($variable, $valor, $asunto);
        }

        return [
            'asunto' => $asunto,
            'contenido' => $contenido,
        ];
    }

    /**
     * Incrementar contador de uso
     */
    public function incrementarUso()
    {
        $this->increment('uso_count');
    }

    /**
     * Obtener lista de variables disponibles
     */
    public static function getVariablesDisponibles()
    {
        return self::$variablesDisponibles;
    }
}
