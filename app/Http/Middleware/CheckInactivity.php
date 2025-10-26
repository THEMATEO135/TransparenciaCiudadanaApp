<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckInactivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (auth()->check()) {
            $timeout = config('session.lifetime') * 60; // Convertir minutos a segundos
            $lastActivity = session('last_activity', time());

            if (time() - $lastActivity > $timeout) {
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();
                return redirect()->route('admin.login')->withErrors(['email' => 'Tu sesión ha expirado por inactividad.']);
            }

            session(['last_activity' => time()]);
        }

        return $next($request);
    }
}
