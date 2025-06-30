<?php

namespace App\Http\Controllers\Api\Admin;

use App\Config;
use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function fetch(Request $request)
    {
        $settings = [];
        $settings['country'] = Country::loadAllFromCache()->where('status', '=', 1);
        $settings['default_country_code'] = Config::DEFAULT_COUNTRY_CODE;

        return makeResponse(true, null, [
            'settings' => $settings,
        ]);
    }
}
