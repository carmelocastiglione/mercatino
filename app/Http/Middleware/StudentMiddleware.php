<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class StudentMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is student (Authenticate middleware handles auth check)
        if (auth()->user()->role === 'studente') {
            return $next($request);
        }

        abort(403, 'Accesso non autorizzato. Solo gli studenti possono accedere a questa area.');
    }
}
