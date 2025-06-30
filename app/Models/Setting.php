<?php

namespace App\Models;

use App\Traits\HasAllCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperSetting
 */
class Setting extends BaseModel
{
    use HasAllCache;
    use HasFactory;
    use SoftDeletes;

    public function getCasts(): array
    {
        return [
            'setting' => 'string',
            'setting_name' => 'string',
            'setting_value' => 'string',
            'setting_type' => 'string',
            'params' => 'array',
        ];
    }

    public static function getSettingTypeLists()
    {
        return [
            'select' => __('Select'),
            'text' => __('Text'),
            'textarea' => __('Textarea'),
            'editor' => __('Editor'),
            'number' => __('Number'),
            'fund' => __('Fund'),
            'image' => __('Image'),
        ];
    }

    public function setSettingValueAttribute($value)
    {
        if ($this->setting_type == 'editor') {
            $this->attributes['setting_value'] = json_decode(json_encode(trim(strip_tags($value))));
        } else {
            $this->attributes['setting_value'] = $value;
        }
    }

    public function getSettingValueAttribute($value)
    {
        if ($this->setting_type == 'editor') {
            return json_decode($value, true, JSON_PRETTY_PRINT);
        } elseif ($this->setting_type == 'image') {
            return htmlspecialchars(urldecode(getUploadStorage()->url(ltrim($value, '/'))));
        } else {
            return $value;
        }
    }
}
