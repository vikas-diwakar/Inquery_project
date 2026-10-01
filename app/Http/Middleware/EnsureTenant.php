<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $company = \App\Models\Company::default();

        if (auth()->check()) {
            $user = auth()->user();
            if (!$user->company_id) {
                $user->company_id = $company->id;
                $user->saveQuietly();
            }
        }

        app()->instance('currentTenant', $company);
        view()->share('currentTenant', $company);

        return $next($request);
    }
}
