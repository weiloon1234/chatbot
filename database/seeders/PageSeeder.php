<?php

namespace Database\Seeders;

use App\Models\Page;
use Illuminate\Database\Seeder;

class PageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getPages() as $var) {
            $count = Page::where('tag', '=', $var['tag'])->count();
            if (! $count) {
                $c = new Page;

                foreach ($var as $k => $v) {
                    $c->$k = $v;
                }

                $c->save();
            }
        }
    }

    public function getPages(): array
    {
        $arr = [];

        $arr[] = [
            'tag' => 'privacy_policy', 'title' => 'Privacy Policy', 'content_en' => '', 'is_system' => 1,
        ];

        return $arr;
    }
}
