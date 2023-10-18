<?php

namespace Althinect\FilamentSpatieRolesPermissions\Middleware;

use Closure;
use Filament\Facades\Filament;
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
        $filament = Filament::getTenant()->id;
        $spatie = getPermissionsTeamId();
        if ($filament !== $spatie) {
            setPermissionsTeamId($filament);
        }

        return $next($request);
    }
}
