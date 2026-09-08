<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SecurityAndPerformanceHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /** @var Response $response */
        $response = $next($request);

        // Standard baseline security headers
        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=()');

        // Cross-Origin-Opener-Policy: Only set on secure/trustworthy origins per W3C specification
        // (Prevents browser console warnings on local non-SSL domains like http://blue-zone.test)
        $isTrustworthyOrigin = $request->isSecure()
            || $request->header('X-Forwarded-Proto') === 'https'
            || in_array($request->getHost(), ['localhost', '127.0.0.1', '::1']);

        if ($isTrustworthyOrigin) {
            $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin');
        } else {
            $response->headers->remove('Cross-Origin-Opener-Policy');
        }

        // HSTS Header (HTTPS only)
        if ($request->isSecure() || $request->header('X-Forwarded-Proto') === 'https') {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy (Report-Only safe baseline)
        if (!$response->headers->has('Content-Security-Policy') && !$response->headers->has('Content-Security-Policy-Report-Only')) {
            $response->headers->set('Content-Security-Policy-Report-Only', "default-src 'self' https: data: 'unsafe-inline' 'unsafe-eval'; frame-ancestors 'self';");
        }

        // Performance: Gzip Compression for text/html/json responses
        if (
            function_exists('gzencode')
            && !($response instanceof BinaryFileResponse)
            && !($response instanceof StreamedResponse)
            && !$response->headers->has('Content-Encoding')
            && str_contains($request->header('Accept-Encoding', ''), 'gzip')
        ) {
            $content = $response->getContent();
            if ($content !== false && strlen($content) > 1024) {
                $compressed = gzencode($content, 6);
                if ($compressed !== false && strlen($compressed) < strlen($content)) {
                    $response->setContent($compressed);
                    $response->headers->set('Content-Encoding', 'gzip');
                    $response->headers->set('Content-Length', (string) strlen($compressed));
                    $response->headers->set('Vary', 'Accept-Encoding', false);
                }
            }
        }

        return $response;
    }
}
