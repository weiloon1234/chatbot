<?php

namespace App\Models;

/**
 * @mixin IdeHelperUserUnilevel
 */
class UserUnilevel extends BaseModel
{
    public static function boot()
    {
        parent::boot();
    }

    public function getCasts(): array
    {
        return [
            'user_id' => 'integer',
            'user_unilevel' => 'integer',
            'introducer_user_id' => 'integer',
            'introducer_unilevel' => 'integer',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function introducer()
    {
        return $this->belongsTo(User::class, 'introducer_user_id', 'id');
    }
}
