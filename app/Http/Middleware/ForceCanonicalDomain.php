<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ForceCanonicalDomain
{
    /**
     * Redirect any request that is not on an allowed host to the canonical
     * domain (e.g. fabiospano.eu), 301.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (app()->isLocal()) {
            return $next($request);
        }

        $allowed = config('site.allowed_hosts');

        if ($allowed === []) {
            return $next($request);
        }

        if (in_array($request->getHost(), $allowed, true)) {
            return $next($request);
        }

        $scheme = config('site.app_scheme');
        $canonical = config('site.canonical_host', $request->getHost());

        return redirect()->away($scheme.'://'.$canonical.$request->getRequestUri(), 301);
    }
}
