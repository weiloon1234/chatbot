<?php

namespace App\Models;

use App\Traits\HasAuditTrail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperAdmin
 */
class Admin extends Authenticatable
{
    use HasApiTokens, HasAuditTrail, HasFactory, Notifiable, SoftDeletes;

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $with = [
        'group',
    ];

    public function getAuditTrailDescription(): string
    {
        $c = explode('\\', get_class($this));

        return end($c).'(#'.$this->id.')';
    }

    public function getCasts(): array
    {
        return [
            'username' => 'string',
            'email' => 'string',
            'name' => 'string',
            'password' => 'string',
            'admin_group_id' => 'integer',
            'type' => 'integer',
            'lang' => 'string',
            'last_login_at' => 'datetime',
        ];
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = sanitizeUsername($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = sanitizePassword($value);
    }

    public function group()
    {
        return $this->hasOne(AdminGroup::class, 'id', 'admin_group_id');
    }

    public static function getTypeLists()
    {
        return [
            0 => __('Admin'),
            1 => __('Super admin'),
            2 => __('Developer'),
        ];
    }

    public function explainType()
    {
        $arr = static::getTypeLists();

        return isset($arr[$this->type]) ? $arr[$this->type] : __('Unknown');
    }

    public function getEnablePermissions()
    {
        if ($this->isSuperAdmin()) {
            // LOAD ALL PERMISSIONS AND ASSUME HAVE ALL OF IT
            return json_decode(file_get_contents(base_path('lang/zh_permission.json')), true, JSON_PRETTY_PRINT);
        } else {
            $had_permissions = $this->group->permissions;
            if (! $had_permissions) {
                return [];
            }

            return array_flip($had_permissions->pluck('permission_tag')->toArray());
        }
    }

    public function hasPermission($permissions)
    {
        if ($this->isSuperAdmin()) {
            return true;
        }

        if (! $this->group) {
            return false;
        }

        $had_permissions = $this->group->permissions;
        if (! $had_permissions) {
            return false;
        }

        $had_permissions = $had_permissions->pluck('permission_tag')->toArray();

        $permissions = ! is_array($permissions) ? [$permissions] : $permissions;

        foreach ($permissions as $p) {
            if (in_array($p, $had_permissions)) {
                return true;
            }
        }

        return false;
    }

    public function isSuperAdmin()
    {
        return in_array($this->type, [1, 2]);
    }

    public function isDeveloper()
    {
        return $this->type == 2;
    }

    public function correctPassword($password)
    {
        return password_verify($password, $this->password);
    }

    public function correctPassword2($password)
    {
        return password_verify($password, $this->password2);
    }

    public function getAvatar()
    {
        return '/img/avatar.png';
    }

    public function getIdentity()
    {
        return $this->name ?? $this->username;
    }

    public function scopeListForAdmin($query)
    {
        return $query->whereIn('type', [0]);
    }

    public function scopeListForSuperAdmin($query)
    {
        return $query->whereIn('type', [0, 1]);
    }

    public function scopeListForDeveloper($query) {}
}
