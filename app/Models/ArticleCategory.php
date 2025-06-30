<?php

namespace App\Models;

use App\Traits\MultiLanguage;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperArticleCategory
 */
class ArticleCategory extends BaseModel
{
    use HasFactory;
    use MultiLanguage;

    public const CACHE_KEY = 'article_categories';

    public function multiLanguageColumns()
    {
        return ['name'];
    }

    public function getCasts(): array
    {
        return [
            'name_en' => 'string',
            'name_zh' => 'string',
            'main_display_style' => 'integer',
            'main_display_show_more' => 'integer',
            'main_display_show_title' => 'integer',
            'list_display_style' => 'integer',
            'details_show_article_cover' => 'integer',
            'details_show_article_datetime' => 'integer',
            'sorting' => 'integer',
        ];
    }

    public static function getMainDisplayStyleLists()
    {
        return [
            1 => __('4 box'),
            2 => __('6 box'),

            11 => __('Carousel view'),

            21 => __('List view'),
        ];
    }

    public static function mainDisplayMethodLoadArticlesCount()
    {
        return [
            1 => 4,
            2 => 6,

            11 => 5,

            21 => 10,
        ];
    }

    public static function getMainDisplayShowMoreLists()
    {
        return getYesNoForSelect();
    }

    public static function getMainDisplayShowTitleLists()
    {
        return getYesNoForSelect();
    }

    public static function getDetailsShowArticleCoverLists()
    {
        return getYesNoForSelect();
    }

    public static function getDetailsShowArticleDatetimeLists()
    {
        return getYesNoForSelect();
    }

    public static function getListDisplayStyleLists()
    {
        return [
            1 => __('Grid view'),

            21 => __('List view'),

            31 => __('Masonry view'),
        ];
    }

    public function scopeSorted($query)
    {
        return $query->orderby('sorting', 'DESC');
    }

    public static function buildCache()
    {
        $arr = [];

        $categories = static::Sorted()
            ->get();

        foreach ($categories as $category) {
            $c = $category;

            $articles = Article::where('article_category_id', $c->id)
                ->Sorted()
                ->take(static::mainDisplayMethodLoadArticlesCount()[$c->main_display_style])
                ->get();

            $c->articles = $articles;

            $arr[] = $c;
        }

        cache()->remember(static::CACHE_KEY, now()->addMinutes(30), function () use ($arr) {
            return $arr;
        });
    }

    public static function getFromCache()
    {
        if (! cache()->has(static::CACHE_KEY)) {
            static::buildCache();
        }

        return cache()->get(static::CACHE_KEY);
    }

    public static function forgetCache()
    {
        cache()->forget(static::CACHE_KEY);
    }
}
