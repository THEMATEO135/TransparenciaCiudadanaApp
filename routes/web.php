<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Admin\ReporteAdminController;
use App\Http\Controllers\Admin\AdminDashboardController; // 👈 Importa el dashboard controller

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

    
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');

    Route::get('/powerbi', function () {
        return view('powerbi');
    });

    // Gestión de reportes
    Route::get('/reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/{reporte}/edit', [ReporteAdminController::class, 'edit'])->name('reportes.edit');
    Route::put('/reportes/{reporte}', [ReporteAdminController::class, 'update'])->name('reportes.update');
    Route::delete('/reportes/{reporte}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');
});
