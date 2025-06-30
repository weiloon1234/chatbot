<?php

namespace App\Models;

use App\Managers\Calculator;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperUserCreditTransfer
 */
class UserCreditTransfer extends BaseModel
{
    use HasFactory;

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($instance) {
            if ($instance->conversion_rate && $instance->from_amount && ! $instance->to_amount) {
                $instance->to_amount = (new Calculator($instance->from_amount))->times($instance->conversion_rate)->getAnswer();
            }
        });
    }

    public function getCasts(): array
    {
        return [
            'from_user_id' => 'integer',
            'to_user_id' => 'integer',
            'from_credit_type' => 'integer',
            'to_credit_type' => 'integer',
            'conversion_rate' => 'float',
            'from_amount' => 'float',
            'to_amount' => 'float',
        ];
    }

    public function fromUser()
    {
        return $this->belongsTo(User::class, 'from_user_id');
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }
}
