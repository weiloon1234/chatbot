<?php

namespace App\Models;

use App\Traits\MultiLanguage;
use App\Traits\UploadFile;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperArticle
 */
class Article extends BaseModel
{
    use HasFactory;
    use MultiLanguage;
    use SoftDeletes;
    use UploadFile;

    public function multiLanguageColumns()
    {
        return [
            'subject',
            'content',
            'description',
            'cover',
        ];
    }

    public function fileColumns()
    {
        return [
            'cover_en' => [
                'path' => '/article/cover_en/',
                'rename' => 'id',
            ],
            'cover_zh' => [
                'path' => '/article/cover_zh/',
                'rename' => 'id',
            ],
        ];
    }

    public function getCasts(): array
    {
        return [
            'sorting' => 'integer',
            'article_category_id' => 'integer',
            'subject_en' => 'string',
            'subject_zh' => 'string',
            'content_en' => 'string',
            'content_zh' => 'string',
            'description_en' => 'string',
            'description_zh' => 'string',
            'cover' => 'string',
            'cover_en' => 'string',
            'cover_zh' => 'string',
        ];
    }

    public function category()
    {
        return $this->belongsTo(ArticleCategory::class, 'article_category_id', 'id');
    }

    public function scopeSorted($query)
    {
        return $query->orderby('sorting', 'DESC');
    }
}
