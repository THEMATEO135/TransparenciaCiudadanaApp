<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Reporte;
use Illuminate\Http\Request;

class ReporteAdminController extends Controller
{
    // Mostrar listado de reportes
    public function index()
    {
        $reportes = Reporte::with('servicio') // Carga la relación
            ->orderBy('created_at', 'DESC')
            ->paginate(10); // Paginación

        return view('admin.reportes.index', compact('reportes'));
    }

    // Mostrar formulario de edición
    public function edit(Reporte $reporte)
    {
        $servicios = \App\Models\Servicio::all(); // Para el select
        return view('admin.reportes.edit', compact('reporte', 'servicios'));
    }

    // Actualizar reporte
    public function update(Request $request, Reporte $reporte)
    {
        $validated = $request->validate([
            'estado' => 'required|string|in:pendiente,en_proceso,resuelto',
            'servicio_id' => 'required|integer|exists:servicios,id',
            'descripcion' => 'required|string',
        ]);

        $reporte->update($validated);

        return redirect()->route('admin.reportes.index')
            ->with('success', 'Reporte actualizado correctamente');
    }

    // Eliminar reporte
    public function destroy(Reporte $reporte)
    {
        $reporte->delete();

        return redirect()->route('admin.reportes.index')
            ->with('success', 'Reporte eliminado correctamente');
    }
}