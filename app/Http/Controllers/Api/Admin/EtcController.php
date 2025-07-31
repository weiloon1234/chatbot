<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EtcController extends Controller
{
    public function loadLog(Request $request)
    {
        if ($request->user() && $request->user()->isDeveloper()) {
            $log = @file_get_contents(storage_path('logs/laravel.log'));

            return makeResponse(true, null, ['log' => $log !== false ? $log : '']);
        } else {
            return makeResponse(true, null, ['log' => __('Permission denied')]);
        }
    }

    public function clearLog(Request $request)
    {
        exec('rm -f '.storage_path('logs/*.log'));
        exec('rm -f '.storage_path('logs/*.gz'));
        Artisan::call('cache:clear');
        Artisan::call('config:clear');

        return makeResponse(true);
    }
}
