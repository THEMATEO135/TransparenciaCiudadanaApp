<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Ciudad;
use Illuminate\Support\Facades\DB;

class ProveedorController extends Controller
{
    /**
     * Obtener proveedores filtrados por ciudad y servicio
     */
    public function getPorCiudadYServicio(Request $request)
    {
        $ciudadId = $request->input('ciudad_id');
        $servicioId = $request->input('servicio_id');

        if (!$ciudadId || !$servicioId) {
            return response()->json([
                'success' => false,
                'message' => 'Se requieren ciudad_id y servicio_id'
            ], 400);
        }

        $proveedores = Proveedor::select('proveedores.*')
            ->join('ciudad_proveedor_servicio', 'proveedores.id', '=', 'ciudad_proveedor_servicio.proveedor_id')
            ->where('ciudad_proveedor_servicio.ciudad_id', $ciudadId)
            ->where('ciudad_proveedor_servicio.servicio_id', $servicioId)
            ->where('ciudad_proveedor_servicio.estado', true)
            ->where('proveedores.estado', true)
            ->get()
            ->map(function($proveedor) {
                return [
                    'id' => $proveedor->id,
                    'nombre' => $proveedor->nombre,
                    'logo_url' => $proveedor->logo_url,
                    'telefono' => $proveedor->telefono,
                    'email' => $proveedor->email,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $proveedores
        ]);
    }

    /**
     * Obtener todas las ciudades activas
     */
    public function getCiudades()
    {
        $ciudades = Ciudad::where('estado', true)
            ->orderBy('nombre')
            ->get(['id', 'nombre', 'departamento']);

        return response()->json([
            'success' => true,
            'data' => $ciudades
        ]);
    }
}
