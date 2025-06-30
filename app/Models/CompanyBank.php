<?php

namespace App\Models;

use App\Traits\MultiLanguage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCompanyBank
 */
class CompanyBank extends BaseModel
{
    use HasFactory, MultiLanguage, SoftDeletes;

    public function multiLanguageColumns()
    {
        return [
            'name',
        ];
    }

    public function getCasts(): array
    {
        return [
            'bank_id' => 'integer',
            'country_id' => 'integer',
            'name_en' => 'string',
            'name_zh' => 'string',
            'account_name' => 'string',
            'account_number' => 'string',
        ];
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function country()
    {
        return $this->belongsTo(Country::class);
    }

    public function fillBank(Bank $bank)
    {
        $this->bank_id = $bank->id;
        foreach (loopLanguageForColumn('name') as $lang) {
            $this->{$lang['column']} = $bank->{$lang['column']};
        }
    }
}
