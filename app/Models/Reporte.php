<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres',
        'correo',
        'telefono',
        'servicio_id',
        'ciudad_id',
        'proveedor_id',
        'descripcion',
        'direccion',
        'localidad',
        'barrio',
        'latitude',
        'longitude',
        'notas_admin',
        'estado_id',
        'prioridad',
        'assigned_to',
        'assigned_at',
        'deadline',
        'sla_hours',
        'parent_id',
        'duplicados_count',
        'imagenes'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'servicio_id' => 'integer',
        'ciudad_id' => 'integer',
        'proveedor_id' => 'integer',
        'estado_id' => 'integer',
        'assigned_to' => 'integer',
        'parent_id' => 'integer',
        'duplicados_count' => 'integer',
        'sla_hours' => 'integer',
        'assigned_at' => 'datetime',
        'deadline' => 'datetime',
        'imagenes' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime'
    ];

    // Relación con servicios
    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
    }

    // Relación con estado
    public function estado()
    {
        return $this->belongsTo(Estado::class, 'estado_id');
    }

    // Relación con ciudad
    public function ciudad()
    {
        return $this->belongsTo(Ciudad::class);
    }

    // Relación con proveedor
    public function proveedor()
    {
        return $this->belongsTo(Proveedor::class);
    }

    // Relación con operador asignado
    public function operador()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    // Relación con reporte padre (si es duplicado)
    public function padre()
    {
        return $this->belongsTo(Reporte::class, 'parent_id');
    }

    // Relación con reportes duplicados
    public function duplicados()
    {
        return $this->hasMany(Reporte::class, 'parent_id');
    }

    // Relación con actualizaciones
    public function updates()
    {
        return $this->hasMany(ReporteUpdate::class)->orderBy('created_at', 'desc');
    }

    // Relación con feedback
    public function feedback()
    {
        return $this->hasOne(ReporteFeedback::class);
    }

    /**
     * Scopes
     */
    public function scopePorEstado($query, $estado)
    {
        // Acepta tanto ID como nombre de estado
        if (is_numeric($estado)) {
            return $query->where('estado_id', $estado);
        }

        // Si es string, buscar por nombre
        $estadoModel = Estado::where('nombre', $estado)->first();
        if ($estadoModel) {
            return $query->where('estado_id', $estadoModel->id);
        }

        return $query;
    }

    public function scopePorPrioridad($query, $prioridad)
    {
        return $query->where('prioridad', $prioridad);
    }

    public function scopeAsignadoA($query, $userId)
    {
        return $query->where('assigned_to', $userId);
    }

    public function scopeSinAsignar($query)
    {
        return $query->whereNull('assigned_to');
    }

    public function scopeDuplicados($query)
    {
        return $query->whereNotNull('parent_id');
    }

    public function scopeMaestros($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeVencidos($query)
    {
        // Obtener IDs de estados finales (resuelto, rechazado, cerrado)
        $estadosFinales = Estado::where('es_estado_final', true)->pluck('id')->toArray();

        return $query->whereNotNull('deadline')
                    ->where('deadline', '<', now())
                    ->whereNotIn('estado_id', $estadosFinales);
    }

    public function scopePorZona($query, $lat, $lng, $radiusKm = 0.5)
    {
        // Búsqueda por coordenadas en radio (Haversine)
        $R = 6371; // Radio de la Tierra en km

        return $query->select('*')
            ->selectRaw("(
                {$R} * acos(
                    cos(radians(?)) *
                    cos(radians(latitude)) *
                    cos(radians(longitude) - radians(?)) +
                    sin(radians(?)) *
                    sin(radians(latitude))
                )
            ) AS distancia", [$lat, $lng, $lat])
            ->having('distancia', '<=', $radiusKm);
    }

    /**
     * Métodos auxiliares
     */
    public function esDuplicado()
    {
        return !is_null($this->parent_id);
    }

    public function tieneDuplicados()
    {
        return $this->duplicados_count > 0;
    }

    public function estaVencido()
    {
        if (!$this->deadline || $this->deadline >= now()) {
            return false;
        }

        // Verificar si está en un estado final
        return $this->estado && !$this->estado->es_estado_final;
    }

    public function estaAsignado()
    {
        return !is_null($this->assigned_to);
    }

    public function calcularPrioridad(bool $registrarUpdate = true)
    {
        $prioridadAnterior = $this->prioridad;
        $prioridad = 'baja';

        // Factor 1: Servicio esencial
        $serviciosEsenciales = [1, 4]; // Energía y Agua
        $esEsencial = in_array($this->servicio_id, $serviciosEsenciales);

        // Factor 2: Tiempo sin resolver
        $horasSinResolver = $this->created_at->diffInHours(now());

        // Factor 3: Cantidad de duplicados (reportes similares)
        $duplicadosCount = $this->duplicados_count;

        // Factor 4: Detectar reportes similares en zona
        // Obtener IDs de estados finales
        $estadosFinales = Estado::where('es_estado_final', true)->pluck('id')->toArray();

        $similares = static::where('servicio_id', $this->servicio_id)
            ->where('id', '!=', $this->id)
            ->where('created_at', '>=', now()->subHours(2))
            ->whereNotIn('estado_id', $estadosFinales)
            ->when($this->latitude && $this->longitude, function($q) {
                $q->porZona($this->latitude, $this->longitude, 0.5);
            })
            ->count();

        // Lógica de priorización
        if ($similares >= 5 || $duplicadosCount >= 5) {
            $prioridad = 'critica';
        } elseif ($esEsencial && $horasSinResolver >= 24) {
            $prioridad = 'critica';
        } elseif ($similares >= 3 || $duplicadosCount >= 3) {
            $prioridad = 'alta';
        } elseif ($esEsencial && $horasSinResolver >= 12) {
            $prioridad = 'alta';
        } elseif ($horasSinResolver >= 48) {
            $prioridad = 'alta';
        } elseif ($esEsencial || $similares >= 1) {
            $prioridad = 'media';
        }

        $this->prioridad = $prioridad;
        $this->save();

        if (
            $registrarUpdate &&
            !is_null($prioridadAnterior) &&
            $prioridadAnterior !== $prioridad
        ) {
            $this->registrarUpdate(
                sprintf(
                    'La prioridad del reporte cambio de %s a %s.',
                    ucfirst($prioridadAnterior),
                    ucfirst($prioridad)
                ),
                'cambio_prioridad'
            );
        }

        return $prioridad;
    }

    public function detectarDuplicados()
    {
        if ($this->esDuplicado()) {
            return null; // Ya es un duplicado, no buscar más
        }

        // Buscar reportes similares
        $similares = static::where('servicio_id', $this->servicio_id)
            ->where('id', '!=', $this->id)
            ->where('created_at', '>=', now()->subHours(24))
            ->whereNull('parent_id') // Solo buscar maestros
            ->when($this->latitude && $this->longitude, function($q) {
                $q->porZona($this->latitude, $this->longitude, 0.5);
            })
            ->when(!$this->latitude || !$this->longitude, function($q) {
                // Si no hay coordenadas, buscar por barrio/localidad
                $q->where(function($query) {
                    if ($this->barrio) {
                        $query->where('barrio', $this->barrio);
                    }
                    if ($this->localidad) {
                        $query->orWhere('localidad', $this->localidad);
                    }
                });
            })
            ->first();

        return $similares;
    }

    public function marcarComoDuplicado($reportePadreId)
    {
        $this->parent_id = $reportePadreId;
        $this->save();

        // Incrementar contador en el padre
        $padre = static::find($reportePadreId);
        if ($padre) {
            $padre->increment('duplicados_count');
            $padre->calcularPrioridad(); // Recalcular prioridad
        }
    }

    public function asignarA($userId, $slaHours = null)
    {
        $estadoAnterior = $this->estado;
        $this->assigned_to = $userId;
        $this->assigned_at = now();

        if ($slaHours) {
            $this->sla_hours = $slaHours;
            $this->deadline = now()->addHours($slaHours);
        }

        $this->estado = 'asignado';
        $this->save();

        // Registrar actualizacion en el timeline
        $this->registrarUpdate(
            "Reporte asignado a " . ($this->operador->name ?? 'operador'),
            'asignacion',
            true,
            [
                'estado_anterior' => $estadoAnterior,
                'estado_nuevo' => 'asignado',
            ]
        );
    }
    public function cambiarEstado($nuevoEstado, $comentario = null)
    {
        $estadoAnterior = $this->estado;

        // Aceptar tanto ID como nombre de estado
        if (is_numeric($nuevoEstado)) {
            $estadoNuevo = Estado::find($nuevoEstado);
        } else {
            $estadoNuevo = Estado::where('nombre', $nuevoEstado)->first();
        }

        if (!$estadoNuevo) {
            throw new \Exception("Estado '{$nuevoEstado}' no encontrado");
        }

        $this->estado_id = $estadoNuevo->id;
        $this->save();

        // Registrar actualizacion en el timeline
        $this->registrarUpdate(
            $comentario ?? "Estado cambiado de '{$estadoAnterior->etiqueta}' a '{$estadoNuevo->etiqueta}'",
            'cambio_estado',
            true,
            [
                'estado_anterior' => $estadoAnterior?->nombre,
                'estado_nuevo' => $estadoNuevo->nombre,
            ]
        );

        // Si se marca como resuelto, crear feedback
        if ($estadoNuevo->nombre === 'resuelto' && !$this->feedback) {
            $token = ReporteFeedback::generarToken();
            ReporteFeedback::create([
                'reporte_id' => $this->id,
                'token' => $token,
            ]);

            // Enviar email de feedback (implementar job)
            // SendFeedbackRequestJob::dispatch($this, $token);
        }
    }

    public function agregarComentario($contenido, $visibleCiudadano = true, $archivoUrl = null)
    {
        return $this->registrarUpdate(
            $contenido,
            $archivoUrl ? 'imagen' : 'comentario',
            $visibleCiudadano,
            [
                'archivo_url' => $archivoUrl,
            ]
        );
    }

    public function getColorPrioridadAttribute()
    {
        return match($this->prioridad) {
            'critica' => 'danger',
            'alta' => 'warning',
            'media' => 'info',
            'baja' => 'success',
            default => 'secondary'
        };
    }

    public function getColorEstadoAttribute()
    {
        // Ahora retornamos el color directamente del estado normalizado
        return $this->estado ? $this->estado->color : '#6c757d';
    }

    public function registrarUpdate(string $mensaje, string $tipo = 'sistema', bool $visibleCiudadano = true, array $extra = []): ReporteUpdate
    {
        $payload = array_merge([
            'reporte_id' => $this->id,
            'user_id' => auth()->id(),
            'tipo' => $tipo,
            'contenido' => $mensaje,
            'visible_ciudadano' => $visibleCiudadano,
        ], $extra);

        return ReporteUpdate::create($payload);
    }
}
