<?php

namespace App\Models;

use App\Traits\MultiLanguage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperPage
 */
class Page extends BaseModel
{
    use HasFactory;
    use MultiLanguage;
    use SoftDeletes;

    protected function multiLanguageColumns()
    {
        return [
            'content',
        ];
    }

    public function getCasts(): array
    {
        return [
            'tag' => 'string',
            'title' => 'string',
            'content_en' => 'string',
            'content_zh' => 'string',
            'is_system' => 'integer',
        ];
    }

    public function setTagAttribute($value)
    {
        $this->attributes['tag'] = trim(strip_tags(mb_strtolower($value)));
    }

    public function setTitleAttribute($value)
    {
        $this->attributes['title'] = trim(strip_tags($value));
    }
}
