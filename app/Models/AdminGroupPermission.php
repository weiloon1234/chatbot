<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAdminGroupPermission
 */
class AdminGroupPermission extends BaseModel
{
    use HasFactory;

    public function getCasts(): array
    {
        return [
            'admin_group_id' => 'integer',
            'permission_tag' => 'string',
        ];
    }

    public function group()
    {
        return $this->belongsTo(AdminGroup::class, 'admin_group_id', 'id');
    }

    public static function getPermissionTagLists()
    {
        $lang = app()->getLocale();
        if ($lang == 'en') {
            // ALWAYS GET CHINESE ADMIN PERMISSION FILE AND BUILD ENGLISH
            $file = json_decode(file_get_contents(base_path('lang/zh_permission.json')), true, JSON_PRETTY_PRINT);
            $t = [];
            foreach ($file as $key => $value) {
                //                $t[str_replace(' ', '_', mb_strtolower($key))] = $key;
                $t[$key] = $key;
            }
        } else {
            $t = json_decode(file_get_contents(base_path('lang/'.app()->getLocale().'_permission.json')), true, JSON_PRETTY_PRINT);
            foreach ($t as $key => $value) {
                //                $t[str_replace(' ', '_', mb_strtolower($key))] = $value;
                $t[$key] = $value;
            }
        }

        return $t;
    }

    public function setPermissionTagAttribute($value)
    {
        $this->attributes['permission_tag'] = trim($value);
    }
}
