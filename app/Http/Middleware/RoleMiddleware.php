<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = $request->user();

        // Dapatkan URL atau nama rute yang diminta
        $target = $request->path(); // Atau $request->route()->getName() untuk nama rute

        // Cek apakah pengguna sedang login dan memiliki salah satu role yang diizinkan
        if (!$user || !in_array($user->role, $roles)) {
            // abort(403, 'Unauthorized access.');
            return redirect('/unauthorized')->with('error', 'anda tidak memiliki hak untuk mengakses halaman '. $target .'.');
        }

        return $next($request);
    }
}
