<?php

namespace App\Models;

use App\Traits\HasAllCache;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @mixin IdeHelperCountry
 */
class Country extends BaseModel
{
    use HasAllCache, HasFactory;

    protected $table = 'countries';

    protected $casts = [
        'iso2' => 'string',
        'iso3' => 'string',
        'name' => 'string',
        'phone_code' => 'string',
        'currency_code' => 'string',
        'currency_symbol_prefix' => 'string',
        'currency_symbol_suffix' => 'string',
        'currency_decimal' => 'integer',
        'rate_to_base' => 'float',
        'status' => 'integer',
    ];

    // 🔹 Relationships
    public function states()
    {
        return $this->hasMany(CountryLocation::class, 'country_id', 'id')
            ->whereNull('parent_id')
            ->Sorted();
    }

    public function area()
    {
        return $this->hasMany(CountryLocation::class, 'country_id', 'id')
            ->whereNotNull('parent_id');
    }

    public function banks()
    {
        return $this->hasMany(Bank::class, 'country_id', 'id');
    }

    // 🔹 Mutators
    public function setNameAttribute($value)
    {
        $this->attributes['name'] = trim(ucfirst($value));
    }

    public function getNameAttribute($value)
    {
        return $value;
    }

    public function setIso2Attribute($value)
    {
        $this->attributes['iso2'] = trim(mb_strtoupper($value));
    }

    public function getIso2Attribute($value)
    {
        return $value;
    }

    public function setIso3Attribute($value)
    {
        $this->attributes['iso3'] = trim(mb_strtoupper($value));
    }

    public function getIso3Attribute($value)
    {
        return $value;
    }

    // 🔹 Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 1);
    }

    // 🔹 Caching
    public function cacheAllQuery()
    {
        return $this->active()
            ->with(['states', 'states.area'])
            ->orderBy('name', 'ASC');
    }

    public static function forgetCache()
    {
        cache()->forget((new self)->getAllCacheKey());
    }

    // 🔹 Utilities
    public static function getCountriesForSelect($cache = false)
    {
        $collections = $cache ? collect(static::loadAllFromCache()) : static::active()->get();

        return $collections->pluck('name', 'id');
    }

    public static function getExtForSelect($cache = false)
    {
        $collections = $cache ? collect(static::loadAllFromCache()) : static::active()->get();

        return $collections->pluck('ext', 'id');
    }

    public static function getStatusLists()
    {
        return [
            0 => __('Inactive'),
            1 => __('Active'),
        ];
    }
}
