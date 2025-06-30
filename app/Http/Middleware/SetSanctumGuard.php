<?php

declare(strict_types=1);

namespace App\Http\Middleware;

use Closure;

class SetSanctumGuard
{
    public function handle($request, Closure $next, $guard)
    {
        config(['sanctum.guard' => $guard]);

        return $next($request);
    }
}
