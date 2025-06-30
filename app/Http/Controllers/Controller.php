<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function __construct()
    {
        if (request()->route() && (\Str::startsWith(request()->route()->getName(), 'admin') || \Str::startsWith(request()->route()->uri, ['api/admin']))) {
            if (! defined('IN_ADMIN')) {
                define('IN_ADMIN', true);
            }
        }
    }
}
