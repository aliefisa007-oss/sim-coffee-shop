<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class NoCacheHtml
{
    /**
     * Cegah browser nge-cache response HTML (halaman blade).
     * Asset hashed di /build/assets tetap cache lama (diatur via .htaccess),
     * cuma HTML-nya yang selalu di-fetch ulang biar reference ke asset selalu yang terbaru.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('Cache-Control', 'no-cache, no-store, must-revalidate');
        $response->headers->set('Pragma', 'no-cache');
        $response->headers->set('Expires', '0');

        return $response;
    }
}
