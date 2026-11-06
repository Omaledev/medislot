<?php

namespace App\Http\Middleware;

use App\Models\Hospital;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Symfony\Component\HttpFoundation\Response;

class ResolveHospitalTenant
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $host = strtolower($request->getHost());

        $hospital = $this->resolveHospitalFromHost($host);

        if ($hospital) {
            $request->attributes->set('tenant_hospital', $hospital);
            app()->instance('tenant_hospital', $hospital);
            View::share('tenantHospital', $hospital);
        } else {
            // If user is authenticated in a session, share their hospital with views
            if ($user = $request->user()) {
                if ($user->hospital) {
                    View::share('tenantHospital', $user->hospital);
                }
            }
        }

        return $next($request);
    }

    /**
     * Attempt to resolve a Hospital from custom domain or subdomain.
     */
    protected function resolveHospitalFromHost(string $host): ?Hospital
    {
        $centralHosts = [
            'localhost',
            '127.0.0.1',
            'medislot.com',
            'medislot.test',
            'www.medislot.com',
            'api.medislot.com',
        ];

        // If it's a known central host without subdomain, return null immediately without querying DB
        if (in_array($host, $centralHosts, true)) {
            return null;
        }

        try {
            // 1. Direct match on custom domain (e.g. his.riversideclinic.org)
            $byCustomDomain = Hospital::where('custom_domain', $host)->first();
            if ($byCustomDomain) {
                return $byCustomDomain;
            }

            // 2. Subdomain check (e.g. riverside.medislot.test, riverside.medislot.com)
            $parts = explode('.', $host);
            if (count($parts) >= 2) {
                $subdomain = $parts[0];
                if (! in_array($subdomain, ['www', 'api', 'admin', 'app', 'mail'], true)) {
                    $bySlug = Hospital::where('slug', $subdomain)->first();
                    if ($bySlug) {
                        return $bySlug;
                    }
                }
            }
        } catch (\Throwable) {
            // Table might not exist yet during migration/initial setup
            return null;
        }

        return null;
    }
}
