<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAdminGroup
 */
class AdminGroup extends BaseModel
{
    use HasFactory;

    protected $with = [
        'permissions',
    ];

    public function getCasts(): array
    {
        return [
            'group_name' => 'string',
        ];
    }

    public function permissions()
    {
        return $this->hasMany(AdminGroupPermission::class, 'admin_group_id', 'id');
    }

    public function admins()
    {
        return $this->hasMany(Admin::class, 'admin_group_id', 'id');
    }

    public static function getPermissionTagLists()
    {
        return AdminGroupPermission::getPermissionTagLists();
    }
}
