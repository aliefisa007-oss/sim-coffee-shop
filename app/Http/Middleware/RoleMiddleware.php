<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        if (!in_array(auth()->user()->role, $roles)) {
            abort(403, 'Akses ditolak.');
        }

        if (!auth()->user()->is_active) {
            auth()->logout();
            return redirect()->route('login')
                             ->with('error', 'Akun Anda dinonaktifkan.');
        }

        return $next($request);
    }
}