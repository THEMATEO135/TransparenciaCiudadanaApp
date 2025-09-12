
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Reporte;
use App\Models\Servicio;

class AdminController extends Controller
{
    public function index() {
        return view('admin.index');
    }

    public function list(Request $request) {
        $query = Reporte::query();

        if ($request->filled('cedula')) {
            $query->where('cedula', $request->cedula);
        }
        if ($request->filled('servicio_id')) {
            $query->where('servicio_id', $request->servicio_id);
        }
        if ($request->filled('estado')) {
            $query->where('estado', $request->estado);
        }

        $reportes = $query->get();
        return view('admin.tabs', compact('reportes'));
    }

    public function show($id) {
        $reporte = Reporte::findOrFail($id);
        return view('admin.show', compact('reporte'));
    }

    public function update(Request $request, $id) {
        $reporte = Reporte::findOrFail($id);
        $reporte->update($request->all());
        return redirect('/admin/reportes')->with('success', 'Reporte actualizado');
    }

    public function destroy($id) {
        Reporte::destroy($id);
        return redirect('/admin/reportes')->with('success', 'Reporte eliminado');
    }
}
