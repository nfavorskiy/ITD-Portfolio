<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $nonce = base64_encode(Str::random(32));
        view()->share('cspNonce', $nonce);

        $response = $next($request);

        $contentType = $response->headers->get('Content-Type');
        if ($contentType && !str_contains($contentType, 'text/html')) {
            return $response;
        }

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('X-XSS-Protection', '1; mode=block');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Content-Security-Policy', $this->buildCspPolicy($nonce));

        if (config('app.env') === 'production') {
            $response->headers->set(
                'Strict-Transport-Security',
                'max-age=31536000; includeSubDomains; preload'
            );
        }

        return $response;
    }

    private function buildCspPolicy(string $nonce): string
    {
        $isLocal = config('app.env') === 'local';

        if ($isLocal) {
            return implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline' 'unsafe-eval' http://127.0.0.1:5173 https://cdn.jsdelivr.net",
                "style-src 'self' 'unsafe-inline' http://127.0.0.1:5173 https://cdn.jsdelivr.net https://fonts.googleapis.com",
                "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com data:",
                "img-src 'self' data: https:",
                "connect-src 'self' ws://127.0.0.1:5173 http://127.0.0.1:5173",
                "form-action 'self'",
                "frame-ancestors 'self'",
                "base-uri 'self'",
                "object-src 'none'",
            ]);
        }

        // Production - strict with nonces
        return implode('; ', [
            "default-src 'self'",
            "script-src 'self' 'nonce-{$nonce}' https://cdn.jsdelivr.net",
            "style-src 'self' 'unsafe-inline' https://cdn.jsdelivr.net https://fonts.googleapis.com",
            "font-src 'self' https://cdn.jsdelivr.net https://fonts.gstatic.com data:",
            "img-src 'self' data: https:",
            "form-action 'self'",
            "frame-ancestors 'self'",
            "base-uri 'self'",
            "object-src 'none'",
            "upgrade-insecure-requests",
        ]);
    }
}