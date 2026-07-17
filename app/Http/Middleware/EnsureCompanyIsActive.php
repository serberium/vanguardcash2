<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureCompanyIsActive
{
    public function handle(Request $request, Closure $next): Response
    {
        
        $user = $request->user();

        // 1. SAdmin não sofre bloqueio, tem acesso total
        if ($user && $user->isSadmin()) {
            return $next($request);
        }

        // 2. Verifica a empresa do usuário
        $company = $user->company;

        if ($company) {
            // Regra: Bloqueia se inativo (false) 
            // OU se a data de validade + 3 dias já passou
            $isExpired = $company->expires_at && $company->expires_at->clone()->addDays(3)->isPast();

            if (!$company->is_active || $isExpired) {
                abort(403, 'Licença expirada. Entre em contato com o suporte.');
            }
        }

        return $next($request);
    }
}