<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Ciudad extends Model
{
    use HasFactory;

    protected $table = 'ciudades';

    protected $fillable = [
        'nombre',
        'departamento',
        'codigo_dane',
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

    // Relación muchos a muchos con proveedores a través de la tabla pivote
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'ciudad_proveedor_servicio')
                    ->withPivot('servicio_id', 'estado')
                    ->withTimestamps();
    }

    // Relación muchos a muchos con servicios a través de la tabla pivote
    public function servicios()
    {
        return $this->belongsToMany(Servicio::class, 'ciudad_proveedor_servicio')
                    ->withPivot('proveedor_id', 'estado')
                    ->withTimestamps();
    }

    // Obtener proveedores para un servicio específico en esta ciudad
    public function proveedoresPorServicio($servicioId)
    {
        return $this->belongsToMany(Proveedor::class, 'ciudad_proveedor_servicio')
                    ->wherePivot('servicio_id', $servicioId)
                    ->wherePivot('estado', true)
                    ->withPivot('servicio_id', 'estado')
                    ->withTimestamps();
    }
}
