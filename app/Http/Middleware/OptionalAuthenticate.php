<?php

namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class OptionalAuthenticate extends Middleware
{
    /**
     * Handle an unauthenticated user.
     *
     * @param  Request  $request
     * @return void
     */
    protected function unauthenticated($request, array $guards) {}
}
