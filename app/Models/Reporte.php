<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

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
        'estado'
    ];

    protected $casts = [
        'latitude' => 'decimal:7',
        'longitude' => 'decimal:7',
        'servicio_id' => 'integer'
    ];

    // Relación con servicios
    public function servicio()
    {
        return $this->belongsTo(Servicio::class);
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
}