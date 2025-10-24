<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Ciudad;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

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
            ->map(function ($proveedor) {
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
     * Obtener todas las ciudades activas junto con la bandera del departamento
     */
    public function getCiudades()
{
    $ciudades = Ciudad::where('estado', true)
        ->orderBy('nombre')
        ->get(['id', 'nombre', 'departamento'])
        ->map(function ($ciudad) {
            $slugCiudad = Str::slug($ciudad->nombre, '-');
            $slugDepto = Str::slug($ciudad->departamento, '-');

            // rutas físicas en public/
            $rutaCiudad = public_path("img/Banderas/Ciudades/{$slugCiudad}.png");
            $rutaDepto = public_path("img/Banderas/Departamentos/{$slugDepto}.png");

            // si existe la bandera de ciudad
            if (file_exists($rutaCiudad)) {
                $ciudad->bandera = asset("img/Banderas/Ciudades/{$slugCiudad}.png");
            }
            // si no, intenta con la del departamento
            elseif (file_exists($rutaDepto)) {
                $ciudad->bandera = asset("img/Banderas/Departamentos/{$slugDepto}.png");
            }
            // si tampoco existe, usa una genérica
            else {
                $ciudad->bandera = asset("img/Banderas/Departamentos/colombia.png");
            }

            return $ciudad;
        });

    return response()->json([
        'success' => true,
        'data' => $ciudades
    ]);
}

}