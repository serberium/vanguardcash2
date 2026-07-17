<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureUserIsSadmin
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Verifica se o usuário logado é sadmin
        if ($request->user() && $request->user()->isSadmin()) {
            return $next($request);
        }
    
        abort(403, 'Acesso não autorizado.');
    }
}
