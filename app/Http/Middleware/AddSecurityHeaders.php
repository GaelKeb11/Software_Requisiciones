<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AddSecurityHeaders
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);
        
        // Remove the X-Powered-By header
        $response->headers->remove('X-Powered-By');

        // Add X-Frame-Options to prevent Clickjacking
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN', false);

        // Add X-Content-Type-Options to prevent MIME-sniffing
        $response->headers->set('X-Content-Type-Options', 'nosniff', false);

        return $response;
    }
}
