<?php

namespace Althinect\FilamentSpatieRolesPermissions\Middleware;

use Althinect\FilamentSpatieRolesPermissions\Support\TenancySupport;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SyncSpatiePermissionsWithFilamentTenants
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        TenancySupport::ensureConfigurationIsValid();
        TenancySupport::syncCurrentTenantTeamContext();

        return $next($request);
    }
}
