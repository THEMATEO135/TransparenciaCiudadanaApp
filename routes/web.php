<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Admin\ReporteAdminController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas públicas (ciudadanos)
Route::get('/', [ReporteController::class, 'index'])->name('home');
Route::get('/reportes/create', [ReporteController::class, 'create'])->name('reportes.create');
Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');
Route::get('/consulta', [ReporteController::class, 'consulta'])->name('reportes.consulta');

// Rutas de administración (protegidas)
Route::prefix('admin')->name('admin.')->middleware(['web'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.reportes.index');
    })->name('dashboard');
Route::get('/powerbi', function () {
    return view('powerbi');
});
    Route::get('/reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/{reporte}/edit', [ReporteAdminController::class, 'edit'])->name('reportes.edit');
    Route::put('/reportes/{reporte}', [ReporteAdminController::class, 'update'])->name('reportes.update');
    Route::delete('/reportes/{reporte}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');
});