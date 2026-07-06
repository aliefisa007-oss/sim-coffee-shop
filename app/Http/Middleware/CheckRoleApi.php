<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckRoleApi
{
    /**
     * Versi API dari RoleMiddleware.
     * Bedanya: return JSON, tidak redirect ke route('login') (karena
     * API tidak punya session/route login berbasis view).
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        if (!$user) {
            return response()->json(['message' => 'Unauthenticated.'], 401);
        }

        if (!$user->is_active) {
            $user->currentAccessToken()->delete();

            return response()->json([
                'message' => 'Akun Anda telah dinonaktifkan.',
            ], 403);
        }

        if (!in_array($user->role, $roles)) {
            return response()->json([
                'message' => 'Anda tidak memiliki akses untuk aksi ini.',
            ], 403);
        }

        return $next($request);
    }
}
