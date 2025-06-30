<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperAccountStatusLog
 */
class AccountStatusLog extends BaseModel
{
    use HasFactory;

    public function getCasts(): array
    {
        return [
            'admin_id' => 'integer',
            'user_id' => 'integer',
            'operation' => 'string',
        ];
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')->withTrashed();
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function setOperationAttribute($value)
    {
        $this->attributes['operation'] = trim(mb_strtolower($value));
    }
}
