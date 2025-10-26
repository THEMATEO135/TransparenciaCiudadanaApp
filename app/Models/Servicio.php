<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
    use HasFactory;

    protected $fillable = ['nombre', 'descripcion'];

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }

    // Relación muchos a muchos con ciudades a través de la tabla pivote
    public function ciudades()
    {
        return $this->belongsToMany(Ciudad::class, 'ciudad_proveedor_servicio')
                    ->withPivot('proveedor_id', 'estado')
                    ->withTimestamps();
    }

    // Relación muchos a muchos con proveedores a través de la tabla pivote
    public function proveedores()
    {
        return $this->belongsToMany(Proveedor::class, 'ciudad_proveedor_servicio')
                    ->withPivot('ciudad_id', 'estado')
                    ->withTimestamps();
    }

    // Obtener proveedores en una ciudad específica para este servicio
    public function proveedoresEnCiudad($ciudadId)
    {
        return $this->belongsToMany(Proveedor::class, 'ciudad_proveedor_servicio')
                    ->wherePivot('ciudad_id', $ciudadId)
                    ->wherePivot('estado', true)
                    ->withPivot('ciudad_id', 'estado')
                    ->withTimestamps();
    }
}