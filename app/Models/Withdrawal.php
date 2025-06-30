<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperWithdrawal
 */
class Withdrawal extends BaseModel
{
    use HasFactory;

    protected $casts = [
        'role' => 'string',
        'user_id' => 'integer',
        'admin_id' => 'integer',
        'credit_type' => 'integer',
        'credit_amount' => 'float',
        'admin_fees' => 'float',
        'conversion_rate' => 'float',
        'currency_amount' => 'float', // with admin fees
        'receivable_currency_amount' => 'float', // without admin fees
        'country_id' => 'integer',
        'bank_id' => 'integer',
        'bank_account_holder_name' => 'string',
        'bank_account_number' => 'string',
        'deposit_method' => 'integer',
        'status' => 'integer',
    ];

    protected $with = [
        'bank',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($instance) {
            if ($instance->conversion_rate) {
                if ($instance->credit_amount && ! $instance->currency_amount) {
                    $instance->currency_amount = $instance->credit_amount * $instance->conversion_rate;
                }

                if ($instance->currency_amount && ! $instance->receivable_currency_amount) {
                    $instance->receivable_currency_amount = $instance->currency_amount - ($instance->admin_fees * $instance->conversion_rate);
                }
            }
        });
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class, 'bank_id', 'id')->withTrashed();
    }

    public function setRoleAttribute($value)
    {
        $this->attributes['role'] = trim(mb_strtolower($value));
    }

    public static function getRoleLists()
    {
        return [
            'user' => __('User'),
            'merchant' => __('Merchant'),
        ];
    }

    public static function getUserCreditTypeLists()
    {
        return User::getCreditTypeLists();
    }

    public function explainUserCreditType()
    {
        $lists = self::getUserCreditTypeLists();

        return $lists[$this->credit_type] ?? __('Unknown');
    }

    public static function getUserWithdrawMethodLists()
    {
        return [
            99 => __('Manual'),
        ];
    }

    public function explainUserWithdrawMethod()
    {
        $lists = self::getUserWithdrawMethodLists();

        return $lists[$this->withdraw_method] ?? __('Unknown');
    }

    public static function getMerchantWithdrawMethodLists()
    {
        return [];
    }

    public function explainMerchantWithdrawMethod()
    {
        $lists = self::getMerchantWithdrawMethodLists();

        return $lists[$this->withdraw_method] ?? __('Unknown');
    }

    public static function getStatusLists()
    {
        return [
            0 => __('Pending'),
            1 => __('Completed'),
            2 => __('Canceled'),
        ];
    }

    public function fillBank(Bank $bank, $bank_account_holder_name, $bank_account_number)
    {
        $this->bank_id = $bank->id;
        $this->bank_account_holder_name = $bank_account_holder_name;
        $this->bank_account_number = $bank_account_number;
    }

    public function fillCountry(Country $country)
    {
        $this->country_id = $country->id;
        $this->conversion_rate = $country->conversion_rate;
    }

    public function markCanceled()
    {
        if ($this->status == 0) {
            $uct = new UserCreditTransaction;
            $uct->user_id = $this->user_id;
            $uct->credit_type = $this->credit_type;
            $uct->transaction_type = 22;
            $uct->amount = $this->credit_amount;
            $uct->related_key = $this->related_key;
            $uct->params = $this->params;
            $uct->save();

            User::where('id', '=', $uct->user_id)
                ->increment('credit_'.$uct->credit_type, $uct->amount);

            $this->status = 1;
        }
    }

    public function toArray()
    {
        $arr = parent::toArray();

        if ($this->role === 'user') {
            $arr['credit_type_explained'] = $this->explainUserCreditType();
            $arr['withdraw_method_explained'] = $this->explainUserWithdrawMethod();
        }

        $arr['status_explained'] = $this->explainStatus();

        return $arr;
    }
}
