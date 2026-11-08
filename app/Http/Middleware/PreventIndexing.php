<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PreventIndexing
{
    /**
     * Handle an incoming request and ensure search engines do not index the response.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');

        return $response;
    }
}
