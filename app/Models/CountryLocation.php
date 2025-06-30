<?php

namespace App\Models;

use App\Traits\MultiLanguage;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @mixin IdeHelperCountryLocation
 */
class CountryLocation extends BaseModel
{
    use MultiLanguage, SoftDeletes;

    public function multiLanguageColumns()
    {
        return [
            'name',
        ];
    }

    public static function boot()
    {
        parent::boot();
    }

    public function getCasts(): array
    {
        return [
            'country_id' => 'integer',
            'parent_id' => 'integer',
            'sorting' => 'integer',
            'name_en' => 'string',
            'name_zh' => 'string',
        ];
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id', 'id');
    }

    public function parent()
    {
        return $this->belongsTo(CountryLocation::class, 'parent_id', 'id');
    }

    public function sons()
    {
        return $this->hasMany(CountryLocation::class, 'parent_id', 'id')->orderBy('sorting', 'ASC');
    }

    public function area()
    {
        return $this->hasMany(CountryLocation::class, 'parent_id', 'id')->orderBy('sorting', 'ASC');
    }

    public function scopeState($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeArea($query)
    {
        return $query->whereNull('parent_id');
    }

    public function scopeSorted($query)
    {
        return $query->orderBy('sorting', 'ASC');
    }

    public static function getFromCache()
    {
        if (! cache()->has('country_location_cache')) {
            cache()->rememberForever('country_location_cache', function () {
                return static::with(['sons'])->State()->Sorted()->get();
            });
        }

        return cache()->get('country_location_cache');
    }

    public static function forgetCache()
    {
        cache()->forget('country_location_cache');
    }
}
