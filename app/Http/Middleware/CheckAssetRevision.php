<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class CheckAssetRevision
{
    public function handle($request, Closure $next, $role)
    {
        if (config('env.APP_ENV') != 'local') {
            $manifest = getAssetManifest($role);

            if (isset($manifest['revision'])) {
                if ($request->header('VREVV')) {
                    if ($request->header('VREVV') != $manifest['revision']) {
                        return makeResponse(418, __('A new version is available, please refresh page'));
                    }
                } else {
                    return makeResponse(401, __('A new version is available, please refresh page'));
                }
            }
        }

        return $next($request);
    }
}
