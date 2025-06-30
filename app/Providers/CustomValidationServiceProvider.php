<?php

namespace App\Providers;

use App\Models\Country;
use App\Services\PhoneService;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Validator;

class CustomValidationServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        Validator::extend('isNumber', function ($attribute, $value, $parameters) {
            return preg_match('/^\d+$/', $value);
        });

        Validator::extend('isPercentage', function ($attribute, $value, $parameters) {
            return preg_match('/^\d+(?:\.\d+)?$/', $value);
        });

        Validator::extend('isFund', function ($attribute, $value, $parameters) {
            return (float) $value > 0;
        });

        Validator::extend('isContactNumber', function ($attribute, $value, $parameters) {
            if (empty($parameters)) {
                return false;
            }

            $countryId = $parameters[0];
            $country = Country::find($countryId);

            if (! $country) {
                return false;
            }

            return PhoneService::validate($value, $country);
        });

        Validator::extend('isUsername', function ($attribute, $value, $parameters) {
            return preg_match('/^[A-Za-z0-9_]{6,32}$/', $value);
        });

        Validator::extend('isPassword', function ($attribute, $value, $parameters) {
            return preg_match('/^(.*?){6,24}$/', $value);
        });

        Validator::extend('isPassword2', function ($attribute, $value, $parameters) {
            return preg_match('/^[0-9]{6}$/', $value);
        });

        Validator::extend('isYesNo', function ($attribute, $value, $parameters) {
            return in_array($value, [0, 1]);
        });
    }
}
