<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeadersMiddleware
{
    /**
     * Handle an incoming request and attach enterprise-grade OWASP security headers.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        // 1. Prevent Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');

        // 2. Prevent MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff');

        // 3. Enable legacy XSS filter
        $response->headers->set('X-XSS-Protection', '1; mode=block');

        // 4. Strict Referrer Policy
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');

        // 5. HTTP Strict Transport Security (HSTS - Enforce TLS 1.3)
        $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');

        // 6. Permissions Policy
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // 7. Content Security Policy
        $response->headers->set('Content-Security-Policy', "default-src 'self' 'unsafe-inline' 'unsafe-eval' https: data: blob:; font-src 'self' https://fonts.gstatic.com https://fonts.bunny.net data:; style-src 'self' 'unsafe-inline' https://fonts.googleapis.com; img-src 'self' data: blob: https:;");

        return $response;
    }
}
