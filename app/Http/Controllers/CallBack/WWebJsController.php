<?php

namespace App\Http\Controllers\CallBack;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WWebJsController extends Controller
{
    public function callBack(Request $request)
    {
        try {
            \Log::info('Callback received from WWebJs');
            \Log::info($request->headers);
            \Log::info($request->all());
        } catch (\Exception $e) {
            \Log::info($e);
        }
    }
}
