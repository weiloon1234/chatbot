<?php

namespace App\Models;

use App\Traits\MultiLanguage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperBank
 */
class Bank extends BaseModel
{
    use HasFactory, MultiLanguage, SoftDeletes;

    public function multiLanguageColumns()
    {
        return [
            'name',
        ];
    }

    public function getCasts(): array
    {
        return [
            'country_id' => 'integer',
            'name_en' => 'string',
            'name_zh' => 'string',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function users()
    {
        return $this->hasMany(User::class, 'bank_id', 'id');
    }
}
