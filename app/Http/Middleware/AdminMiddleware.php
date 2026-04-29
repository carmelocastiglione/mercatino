<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if user is admin (Authenticate middleware handles auth check)
        if (auth()->user()->role === 'admin') {
            return $next($request);
        }

        abort(403, 'Accesso non autorizzato. Solo gli amministratori possono accedere a questa area.');
    }
}
