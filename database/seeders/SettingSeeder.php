<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getSettings() as $var) {
            $count = Setting::where('setting', '=', $var['setting'])->count();
            if (! $count) {
                $c = new Setting;

                foreach ($var as $k => $v) {
                    $c->$k = $v;
                }

                $c->save();
            }
        }
    }

    public function getSettings(): array
    {
        $arr = [];

        //        $arr[] = ['setting' => 'about_us', 'setting_name' => 'About us (English)', 'setting_value' => '<p>HELLO</p>', 'setting_type' => 'editor'];
        //        $arr[] = ['setting' => 'facebook_url', 'setting_name' => 'Facebook', 'setting_value' => 'https://facebook.com', 'setting_type' => 'text'];
        //        $arr[] = ['setting' => 'whats_new_banner', 'setting_name' => 'What\'s new banner', 'setting_value' => 'cuploads/setting/whats_new_banner.jpg?t=1', 'setting_type' => 'image'];

        return $arr;
    }
}
