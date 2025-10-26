<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

class Proveedor extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'proveedores';

    protected $fillable = [
        'nombre',
        'logo_url',
        'sitio_web',
        'telefono',
        'email',
        'descripcion',
        'estado'
    ];

    protected $casts = [
        'estado' => 'boolean'
    ];

    // Relación con reportes
    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    // Relación muchos a muchos con ciudades a través de la tabla pivote
    public function ciudades()
    {
        return $this->belongsToMany(Ciudad::class, 'ciudad_proveedor_servicio')
                    ->withPivot('servicio_id', 'estado')
                    ->withTimestamps();
    }

    // Relación muchos a muchos con servicios a través de la tabla pivote
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'ciudad_proveedor_servicio')
                    ->withPivot('ciudad_id', 'estado')
                    ->withTimestamps();
    }

    // Obtener ciudades donde opera este proveedor para un servicio específico
    public function ciudadesPorServicio($servicioId)
    {
        return $this->belongsToMany(Ciudad::class, 'ciudad_proveedor_servicio')
                    ->wherePivot('servicio_id', $servicioId)
                    ->wherePivot('estado', true)
                    ->withPivot('servicio_id', 'estado')
                    ->withTimestamps();
    }

}
