<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Servicio extends Model
{
    protected $fillable = ['nombre', 'descripcion'];

    public function reportes()
    {
        return $this->hasMany(Reporte::class);
    }
}