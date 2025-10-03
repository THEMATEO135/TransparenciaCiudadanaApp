<?php
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ReporteController;
use App\Http\Controllers\Admin\ReporteAdminController;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\LoginController;
use App\Http\Controllers\Admin\PasswordResetController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\NotificationController;
use App\Http\Controllers\Admin\DashboardStatsController;
use App\Http\Controllers\HistorialReportesController;
use App\Http\Controllers\Api\ProveedorController as ApiProveedorController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Rutas API
Route::prefix('api')->name('api.')->group(function () {
    Route::get('/proveedores', [ApiProveedorController::class, 'getPorCiudadYServicio'])->name('proveedores.filtrar');
    Route::get('/ciudades', [ApiProveedorController::class, 'getCiudades'])->name('ciudades.listar');
});

// Rutas públicas (ciudadanos)
Route::get('/', [ReporteController::class, 'index'])->name('home');
Route::get('/reportes/create', [ReporteController::class, 'create'])->name('reportes.create');
Route::post('/reportes', [ReporteController::class, 'store'])->name('reportes.store');
Route::get('/consulta', [ReporteController::class, 'consulta'])->name('reportes.consulta');

// Rutas de historial con OTP
Route::get('/mis-reportes', [HistorialReportesController::class, 'index'])->name('reportes.historial');
Route::post('/mis-reportes/enviar-otp', [HistorialReportesController::class, 'enviarOtp'])->name('reportes.historial.enviarOtp');
Route::get('/mis-reportes/verificar', [HistorialReportesController::class, 'mostrarVerificacion'])->name('reportes.historial.verificar');
Route::post('/mis-reportes/verificar', [HistorialReportesController::class, 'verificarOtp'])->name('reportes.historial.verificarOtp');

// Rutas de autenticación admin
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

    // Recuperación de contraseña
    Route::get('/forgot-password', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/forgot-password', [PasswordResetController::class, 'sendResetLink'])->name('password.email');
    Route::get('/reset-password/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [PasswordResetController::class, 'reset'])->name('password.update');
});

// Rutas de administración (protegidas)
Route::prefix('admin')->name('admin.')->middleware(['admin.auth'])->group(function () {
    Route::get('/', [AdminDashboardController::class, 'index'])->name('dashboard');
    Route::get('/powerbi', function () {
        return view('powerbi');
    });

    // Gestión de reportes
    Route::get('/reportes', [ReporteAdminController::class, 'index'])->name('reportes.index');
    Route::get('/reportes/{reporte}/edit', [ReporteAdminController::class, 'edit'])->name('reportes.edit');
    Route::put('/reportes/{reporte}', [ReporteAdminController::class, 'update'])->name('reportes.update');
    Route::delete('/reportes/{reporte}', [ReporteAdminController::class, 'destroy'])->name('reportes.destroy');

    // Mapa de reportes
    Route::get('/mapa', [AdminDashboardController::class, 'mapa'])->name('mapa');

    // Exportaciones
    Route::get('/reportes/export/excel', [ReporteAdminController::class, 'exportExcel'])->name('reportes.export.excel');
    Route::get('/reportes/export/pdf', [ReporteAdminController::class, 'exportPdf'])->name('reportes.export.pdf');

    // 2FA
    Route::get('/2fa/enable', [TwoFactorController::class, 'enable'])->name('2fa.enable');
    Route::post('/2fa/verify', [TwoFactorController::class, 'verify'])->name('2fa.verify');
    Route::post('/2fa/disable', [TwoFactorController::class, 'disable'])->name('2fa.disable');

    // Notificaciones
    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::get('/notifications/unread', [NotificationController::class, 'unread'])->name('notifications.unread');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.read');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.readAll');

    // API para actualizaciones en tiempo real
    Route::get('/dashboard/stats', [DashboardStatsController::class, 'stats'])->name('dashboard.stats');
});
