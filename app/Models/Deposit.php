<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperDeposit
 */
class Deposit extends BaseModel
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
        'currency_amount' => 'float',
        'country_id' => 'integer',
        'company_bank_id' => 'integer',
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
            if ($instance->credit_amount && $instance->conversion_rate && ! $instance->currency_amount) {
                $instance->currency_amount = $instance->credit_amount * $instance->conversion_rate;
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

    public function companyBank()
    {
        return $this->belongsTo(CompanyBank::class, 'company_bank_id', 'id')->withTrashed();
    }

    public function receipts()
    {
        return $this->hasMany(DepositReceipt::class, 'deposit_id', 'id');
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

    public static function getUserDepositMethodLists()
    {
        return [
            99 => __('Manual'),
        ];
    }

    public function explainUserDepositMethod()
    {
        $lists = self::getUserDepositMethodLists();

        return $lists[$this->deposit_method] ?? __('Unknown');
    }

    public static function getMerchantDepositMethodLists()
    {
        return [];
    }

    public function explainMerchantDepositMethod()
    {
        $lists = self::getMerchantDepositMethodLists();

        return $lists[$this->deposit_method] ?? __('Unknown');
    }

    public static function getStatusLists()
    {
        return [
            0 => __('Pending'),
            1 => __('Completed'),
            2 => __('Canceled'),
        ];
    }

    public function fillCompanyBank(CompanyBank $companyBank)
    {
        $this->company_bank_id = $companyBank->id;
        $this->bank_id = $companyBank->bank_id;
        $this->bank_account_holder_name = $companyBank->account_name;
        $this->bank_account_number = $companyBank->account_number;
    }

    public function fillCountry(Country $country)
    {
        $this->country_id = $country->id;
        $this->conversion_rate = $country->conversion_rate;
    }

    public function markCompleted()
    {
        if ($this->status == 0) {
            $uct = new UserCreditTransaction;
            $uct->user_id = $this->user_id;
            $uct->credit_type = $this->credit_type;
            $uct->transaction_type = 3;
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
            $arr['deposit_method_explained'] = $this->explainUserDepositMethod();
        }
        $arr['status_explained'] = $this->explainStatus();

        return $arr;
    }
}
