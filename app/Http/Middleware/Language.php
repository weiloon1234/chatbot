<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class Language
{
    public function handle(Request $request, Closure $next, $guard = null)
    {
        $lang = $request->header('Accept-Language', config('app.fallback_locale'));
        if ($guard) {
            $lang = auth($guard)->user()->lang ?? $lang;
        }
        app()->setLocale($lang);

        return $next($request);
    }
}
