<?php

namespace App\Models;

/**
 * @mixin IdeHelperUserCreditTransaction
 */
class UserCreditTransaction extends BaseModel
{
    public static function boot()
    {
        parent::boot();
    }

    public function getCasts(): array
    {
        return [
            'user_id' => 'integer',
            'credit_type' => 'integer',
            'transaction_type' => 'integer',
            'amount' => 'float',
            'related_key' => 'string',
            'params' => 'array',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function adminAdjustUserCredit()
    {
        return $this->belongsTo(AdminAdjustUserCredit::class, 'user_credit_transaction_id', 'id');
    }

    public static function getTransactionTypeLists($params = [])
    {
        return [
            1 => __('Admin add', $params),
            2 => __('Admin subtract', $params),

            3 => __('Transaction deposit', $params),

            11 => __('Transaction transfer credit out', $params),
            12 => __('Transaction transfer credit in', $params),

            21 => __('Transaction withdraw', $params),
            22 => __('Transaction withdraw refund', $params),
        ];
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
