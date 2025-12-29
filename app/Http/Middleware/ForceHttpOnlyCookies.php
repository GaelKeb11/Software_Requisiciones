<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceHttpOnlyCookies
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        foreach ($response->headers->getCookies() as $cookie) {
            // Create a new cookie with the same properties, but with HttpOnly set to true
            $newCookie = new \Symfony\Component\HttpFoundation\Cookie(
                $cookie->getName(),
                $cookie->getValue(),
                $cookie->getExpiresTime(),
                $cookie->getPath(),
                $cookie->getDomain(),
                $cookie->isSecure(),
                true, // Force HttpOnly
                $cookie->isRaw(),
                $cookie->getSameSite()
            );

            // Replace the old cookie with the new one
            $response->headers->setCookie($newCookie);
        }

        return $response;
    }
}
