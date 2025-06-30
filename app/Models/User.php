<?php

namespace App\Models;

use App\Traits\HasContactNumber;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

/**
 * @mixin IdeHelperUser
 */
class User extends Authenticatable
{
    use HasApiTokens, HasContactNumber, HasFactory, Notifiable;

    const DEFAULT_PASSWORD = 'qweasd';

    const DEFAULT_PASSWORD2 = 123123;

    protected $hidden = [
        'password',
        'password2',
        'remember_token',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($instance) {
            if (! $instance->password) {
                $instance->password = static::DEFAULT_PASSWORD;
            }
            if (! $instance->password2) {
                $instance->password2 = static::DEFAULT_PASSWORD2;
            }
            if (! $instance->last_login_at) {
                $instance->last_login_at = now();
            }
        });
    }

    protected $appends = [
        'identity',
    ];

    public function getCasts(): array
    {
        return [
            'id' => 'integer',
            'username' => 'string',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'datetime',
            'password' => 'string',
            'password2' => 'string',
            'country_id' => 'integer',
            'contact_country_id' => 'integer',
            'contact_number' => 'string',
            'full_contact_number' => 'string',
            'introducer_user_id' => 'integer',
            'unilevel' => 'integer',
            'lang' => 'string',
            'avatar' => 'string',
            'credit_1' => 'float',
            'credit_2' => 'float',
            'credit_3' => 'float',
            'credit_4' => 'float',
            'credit_5' => 'float',
            'bank_id' => 'integer',
            'bank_account_name' => 'string',
            'bank_account_number' => 'string',
            'national_id' => 'string',
            'first_login' => 'integer',
            'ban_until' => 'datetime',
            'ban' => 'integer',
            'new_login_at' => 'datetime',
            'last_login_at' => 'datetime',
        ];
    }

    public static function getCreditTypeLists()
    {
        return [
            1 => __('Credit 1'),
            2 => __('Credit 2'),
        ];
    }

    public function introducer()
    {
        return $this->belongsTo(User::class, 'introducer_user_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function bank()
    {
        return $this->hasOne(Bank::class, 'id', 'bank_id');
    }

    public function unilevels()
    {
        return $this->hasMany(UserUnilevel::class, 'user_id', 'id');
    }

    public function uniqueUnilevels()
    {
        return $this->hasMany(UserUnilevel::class, 'user_id', 'id')->groupBy('introducer_user_id');
    }

    public function downlines()
    {
        return $this->hasMany(UserUnilevel::class, 'introducer_user_id', 'id');
    }

    public function uniqueDownlines()
    {
        return $this->hasMany(UserUnilevel::class, 'introducer_user_id', 'id')->groupBy('user_id');
    }

    public function creditTransactions()
    {
        return $this->hasMany(UserCreditTransaction::class, 'user_id', 'id');
    }

    // FIND $ID is in same hierarchy
    public function userInSameHierarchy($id)
    {
        return static::where('id', '=', $this->id)
            ->where(function ($q) use ($id) {
                $q->orWhere(function ($iq) use ($id) {
                    $iq->whereHas('sponsorUnilevels', function ($iiq) use ($id) {
                        $iiq->where('user_id', '=', $id);
                    });
                })->orWhere(function ($iq) use ($id) {
                    $iq->whereHas('unilevels', function ($iiq) use ($id) {
                        $iiq->where('sponsor_user_id', '=', $id);
                    });
                });
            })->exists();
    }

    // FIND $ID downline in my X GEN, false if not the same hierarchy
    public function userIdIsXGenOfMe($id)
    {
        $tree = $this->sponsorUnilevels()->where('user_id', '=', $id)->first();

        if (! $tree) {
            return false;
        }

        if ($tree->sponsor_unilevel > $tree->user_unilevel) {
            return false;
        }

        return $tree->user_unilevel - $tree->sponsor_unilevel;
    }

    // FIND $ID is X GEN upline, false if not the same hierarchy
    public function meIsXGenOfUserId($id)
    {
        $tree = $this->unilevels()->where('sponsor_user_id', '=', $id)->first();

        if (! $tree) {
            return false;
        }

        if ($tree->sponsor_unilevel > $tree->user_unilevel) {
            return false;
        }

        return $tree->user_unilevel - $tree->sponsor_unilevel;
    }

    // FIND all downline IDS
    public function getUnilevelDownlinesIds()
    {
        return $this->uniqueDownlines()->pluck('user_id');
    }

    // FIND all upline IDS
    public function getUnilevelUplinesIds()
    {
        return $this->unilevels()->pluck('sponsor_user_id');
    }

    // COUNT DOWNLINE PAX
    public function countUnilevelDownlinePaxs()
    {
        return $this->uniqueDownlines()->count();
    }

    // COUNT UPLINE PAX
    public function countUnilevelUplinePaxs()
    {
        return $this->uniqueUnilevels()->count();
    }

    // Find specified gen upline
    public function findXGenUser($gen)
    {
        return static::whereHas('uniqueDownlines', function ($iiq) use ($gen) {
            $iiq->where('user_id', '=', $this->id)
                ->where('introducer_unilevel', '=', $this->unilevel - $gen);
        })->first();
    }

    public function getCredit($id)
    {
        $field = 'credit_'.$id;

        return $this->$field;
    }

    public function isOrigin()
    {
        return $this->username == 'origin' || $this->id == 1;
    }

    public function setUsernameAttribute($value)
    {
        $this->attributes['username'] = sanitizeUsername($value);
    }

    public function setPasswordAttribute($value)
    {
        $this->attributes['password'] = sanitizePassword($value);
    }

    public function setPassword2Attribute($value)
    {
        $this->attributes['password2'] = sanitizePassword($value);
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

    public function getIdentityAttribute($value)
    {
        return $this->username ?? $this->full_contact_number ?? $this->name ?? $this->id;
    }
}
