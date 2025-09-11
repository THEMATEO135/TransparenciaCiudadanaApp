<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Reporte extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombres', 'correo', 'telefono', 'cedula', 'servicio_id', 'descripcion', 'direccion', 'barrio', 'localidad', 'latitud', 'longitud', 'estado'
    ];
}
