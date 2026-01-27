<?php

namespace App\Http\Middleware;

use App\Models\Company;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class IdentifyTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Try to identify tenant from header
        $tenantId = $request->header('X-Tenant-ID');

        if (!$tenantId) {
            // Try from subdomain
            $domain = $request->getHost();
            $company = Company::whereHas('domains', function ($query) use ($domain) {
                $query->where('domain', $domain);
            })->first();

            if ($company) {
                $tenantId = $company->id;
            }
        }

        if (!$tenantId) {
            return response()->json([
                'message' => 'Tenant not identified. Please provide X-Tenant-ID header or use tenant subdomain.',
            ], 400);
        }

        $company = Company::find($tenantId);

        if (!$company) {
            return response()->json([
                'message' => 'Tenant not found',
            ], 404);
        }

        if (!$company->isActive()) {
            return response()->json([
                'message' => 'Company is not active',
            ], 403);
        }

        if (!$company->hasValidSubscription()) {
            return response()->json([
                'message' => 'Company subscription has expired',
            ], 403);
        }

        // Initialize tenant
        tenancy()->initialize($company);

        return $next($request);
    }
}
