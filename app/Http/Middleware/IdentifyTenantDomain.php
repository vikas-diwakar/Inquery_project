<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenantDomain
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $subdomain = $this->extractSubdomain($request);

        if (!$subdomain) {
            // Root domain - no tenant subdomain
            view()->share('currentTenant', null);
            return $next($request);
        }

        // Check if reserved
        if (Company::isReservedSubdomain($subdomain)) {
            view()->share('currentTenant', null);
            return $next($request);
        }

        // Find tenant by subdomain
        $tenant = Company::bySubdomain($subdomain)->first();

        if (!$tenant) {
            return response()->view('errors.tenant-not-found', [
                'subdomain' => $subdomain,
            ], 404);
        }

        if (!$tenant->is_active) {
            return response()->view('errors.tenant-suspended', [
                'tenant' => $tenant,
            ], 403);
        }

        // Bind current tenant to application container & share with Blade views
        app()->instance('currentTenant', $tenant);
        view()->share('currentTenant', $tenant);

        // Cross-tenant access isolation for authenticated users
        if (auth()->check()) {
            $user = auth()->user();
            if ($user->company_id && (int) $user->company_id !== (int) $tenant->id) {
                // User is authenticated under a different company
                auth()->logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return redirect()->route('login')
                    ->with('error', "Your account does not belong to {$tenant->name}. Please log in with your credentials for this workspace.");
            }
        }

        return $next($request);
    }

    /**
     * Extract the subdomain from request host or fallback query/path
     */
    protected function extractSubdomain(Request $request): ?string
    {
        $host = $request->getHost();

        // 1. Support *.localhost (e.g. acme.localhost)
        if (str_ends_with($host, '.localhost')) {
            $sub = substr($host, 0, -strlen('.localhost'));
            if (!empty($sub)) {
                return strtolower($sub);
            }
        }

        // 2. Support configured APP_DOMAIN or base domain (e.g. acme.propdrip.com or acme.propdrip.test)
        $configuredDomain = config('app.domain') ?: config('session.domain');
        if ($configuredDomain) {
            $cleanDomain = ltrim($configuredDomain, '.');
            if (str_ends_with($host, '.' . $cleanDomain)) {
                $sub = substr($host, 0, -strlen('.' . $cleanDomain));
                if (!empty($sub)) {
                    return strtolower($sub);
                }
            }
        }

        // 3. Fallback for domain with multiple segments (e.g. acme.example.com)
        $parts = explode('.', $host);
        // If not an IP address and has at least 3 parts (sub.domain.tld)
        if (!filter_var($host, FILTER_VALIDATE_IP) && count($parts) >= 3) {
            $sub = $parts[0];
            return strtolower($sub);
        }

        // 4. Friendly fallback for local testing without local DNS (e.g. ?workspace=acme)
        if ($request->has('workspace')) {
            return strtolower(trim($request->query('workspace')));
        }

        return null;
    }
}
