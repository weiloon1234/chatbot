<?php

namespace Database\Seeders;

use App\Config;
use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('env.APP_DEBUG') !== true) {
            throw new \Exception(__('Permission denied'));
        }

        $default_country = \App\Models\Country::where('iso2', '=', Config::DEFAULT_COUNTRY_CODE)
            ->first();

        $user = new User;
        $user->id = 1;
        $user->username = 'origin';
        $user->name = 'Origin';
        $user->email = 'origin@ask.com';
        $user->password = '123456789000';
        $user->password2 = '123456';
        $user->unilevel = 1;
        if ($default_country) {
            $user->country_id = $default_country->id;
            $user->contact_country_id = $default_country->id;
            $user->contact_number = '166885055';
        }
        $user->save();
    }
}
