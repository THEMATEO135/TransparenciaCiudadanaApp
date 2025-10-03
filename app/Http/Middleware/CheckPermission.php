<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Tabla de permisos por rol
     */
    protected $permissions = [
        'admin' => [
            'reportes.view',
            'reportes.create',
            'reportes.edit',
            'reportes.delete',
            'reportes.export',
            'users.view',
            'users.create',
            'users.edit',
            'users.delete',
            'dashboard.view',
            'dashboard.stats',
            'logs.view',
            'settings.edit'
        ],
        'supervisor' => [
            'reportes.view',
            'reportes.edit',
            'reportes.export',
            'dashboard.view',
            'dashboard.stats',
            'logs.view'
        ],
        'operador' => [
            'reportes.view',
            'reportes.edit',
            'dashboard.view'
        ]
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $permission): Response
    {
        if (!auth()->check()) {
            return redirect()->route('admin.login');
        }

        $user = auth()->user();

        if (!$user->is_active) {
            auth()->logout();
            return redirect()->route('admin.login')->withErrors(['email' => 'Tu cuenta está inactiva.']);
        }

        // Admin tiene todos los permisos
        if ($user->role === 'admin') {
            return $next($request);
        }

        // Verificar si el rol tiene el permiso requerido
        $rolePermissions = $this->permissions[$user->role] ?? [];

        if (!in_array($permission, $rolePermissions)) {
            abort(403, 'No tienes permisos para realizar esta acción.');
        }

        return $next($request);
    }

    /**
     * Verificar si un usuario tiene un permiso específico
     */
    public static function hasPermission($user, $permission): bool
    {
        $middleware = new self();

        if ($user->role === 'admin') {
            return true;
        }

        $rolePermissions = $middleware->permissions[$user->role] ?? [];
        return in_array($permission, $rolePermissions);
    }
}
