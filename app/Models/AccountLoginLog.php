<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAccountLoginLog
 */
class AccountLoginLog extends BaseModel
{
    use HasFactory;

    public bool $disabled_audit_trail = true;

    public static function boot()
    {
        parent::boot();
        static::creating(function ($q) {
            if (! $q->ip_address) {
                $q->ip_address = request()->getClientIp();
            }
        });
    }

    public function getCasts(): array
    {
        return [
            'model' => 'string',
            'model_id' => 'integer',
            'login_type' => 'string',
            'ip_address' => 'string',
        ];
    }

    public function setAccount($model)
    {
        $this->model = getClass($model);
        $this->model_id = $model->id;
    }

    public function setLoginTypeAttribute($value)
    {
        $this->attributes['login_type'] = trim(mb_strtolower($value));
    }
}
