<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('env.APP_DEBUG') !== true) {
            throw new \Exception(__('Permission denied'));
        }

        // \App\Models\User::factory(10)->create();
        $admin = new AdminSeeder;
        $country = new CountrySeeder;
        $location = new CountryLocationSeeder;
        $bank = new BankSeeder;
        $user = new UserSeeder;
        $setting = new SettingSeeder;
        $page = new PageSeeder;

        $admin->run();
        $country->run();
        $location->run();
        $bank->run();
        $user->run();
        $setting->run();
        $page->run();
    }
}
