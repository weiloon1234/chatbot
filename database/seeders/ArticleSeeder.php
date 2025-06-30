<?php

namespace Database\Seeders;

use App\Models\ArticleCategory;
use Illuminate\Database\Seeder;

class ArticleSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (config('env.APP_DEBUG') !== true) {
            throw new \Exception(__('Permission denied'));
        }

        ArticleCategory::forgetCache();
        ArticleCategory::buildCache();
    }
}
