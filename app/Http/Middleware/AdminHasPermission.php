<?php

namespace App\Http\Middleware;

use App\Models\Admin;
use Closure;
use Illuminate\Http\Request;

class AdminHasPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  string|null  ...$guards
     * @return mixed
     */
    public function handle(Request $request, Closure $next, ...$permissions)
    {
        if (request()->user() && request()->user() instanceof Admin) {
            if (is_string($permissions) && \Str::of($permissions)->contains(',')) {
                $permissions = explode(',', $permissions);
            } else {
                $permissions = empty($permissions) ? [null] : $permissions;
            }

            foreach ($permissions as $permission) {
                if (! isEmpty($permission) && request()->user()->hasPermission($permission)) {
                    return $next($request);
                }
            }
        }

        return makeResponse(false, __('Permission denied'));
    }
}
