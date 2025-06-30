<?php

namespace App\Traits;

trait HasAllCache
{
    protected function getAllCacheKey()
    {
        return get_class($this).'_all_cache';
    }

    protected function cacheMinute()
    {
        return 'forever';
    }

    public static function bootHasAllCache()
    {
        static::saved(function ($q) {
            cache()->forget($q->getAllCacheKey());
        });
    }

    public static function loadAllFromCache()
    {
        $c = new self;

        if (! cache()->has($c->getAllCacheKey())) {
            if ($c->cacheMinute() === 'forever') {
                cache()->rememberForever($c->getAllCacheKey(), function () use ($c) {
                    if (method_exists($c, 'cacheAllQuery')) {
                        return $c->cacheAllQuery()->get();
                    } else {
                        return $c->orderBy('id', 'DESC')->get();
                    }
                });
            } else {
                cache()->put($c->getAllCacheKey(), function () use ($c) {
                    if (method_exists($c, 'cacheAllQuery')) {
                        return $c->cacheAllQuery()->get();
                    } else {
                        return $c->orderBy('id', 'DESC')->get();
                    }
                }, now()->addMinutes($c->cacheMinute()));
            }
        }

        return cache()->get($c->getAllCacheKey());
    }
}
