<?php

namespace App\Models;

/**
 * @mixin IdeHelperAdminAdjustUserCredit
 */
class AdminAdjustUserCredit extends BaseModel
{
    public static function boot()
    {
        parent::boot();
    }

    public function getCasts(): array
    {
        return [
            'user_id' => 'integer',
            'admin_id' => 'integer',
            'user_credit_transaction_id' => 'integer',
            'credit_type' => 'integer',
            'transaction_type' => 'integer',
            'amount' => 'float',
            'related_key' => 'string',
            'params' => 'array',
            'remark' => 'string',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id')->withTrashed();
    }

    public function transaction()
    {
        return $this->hasOne(AdminAdjustUserCredit::class, 'id', 'user_credit_transaction_id');
    }

    public static function getTransactionTypeLists($params = [])
    {
        return [
            1 => __('Admin add', $params),
            2 => __('Admin subtract', $params),
        ];
    }

    public static function getCreditTypeLists()
    {
        return User::getCreditTypeLists();
    }

    public function explainTransactionType()
    {
        return static::getTransactionTypeLists($this->params)[$this->transaction_type] ?? __('Unknown');
    }

    public function toArray()
    {
        $arr = parent::toArray();
        $arr['transaction_type_explained'] = $this->explainTransactionType();

        return $arr;
    }
}
