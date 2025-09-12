<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\AdminController;


Route::get('/', [ReporteController::class, 'index']);
Route::post('/reportar', [ReporteController::class, 'store']);
Route::get('/consulta', [ReporteController::class, 'consulta']);
Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');
Route::get('/admin', [AdminController::class, 'index']);
Route::get('/admin/reportes', [AdminController::class, 'list']);
Route::get('/admin/reportes/{id}', [AdminController::class, 'show']);
Route::put('/admin/reportes/{id}', [AdminController::class, 'update']);
Route::delete('/admin/reportes/{id}', [AdminController::class, 'destroy']);
