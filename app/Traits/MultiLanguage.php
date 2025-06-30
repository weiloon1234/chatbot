<?php

namespace App\Traits;

use Illuminate\Http\Request;

trait MultiLanguage
{
    public static function booting()
    {
        static::saving(function ($m) {
            if (method_exists($m, 'multiLanguageColumns') && count($m->multiLanguageColumns())) {
                foreach ($m->multiLanguageColumns() as $column) {
                    foreach (config('app.locales') as $key => $locale) {
                        $field = $column.'_'.$key;
                        if ($key != config('app.fallback_locale')) {
                            if (isEmpty($m->$field) || $m->$field == 'null') {
                                $default = $column.'_'.config('app.fallback_locale');
                                if ($m->$default != null) {
                                    $m->$field = $m->$default;
                                }
                            }
                        }
                    }
                }
            }
        });
    }

    public function loopAndSetMultiLanguageColumn(Request $request, string $column_name, $default_value = null)
    {
        foreach (loopLanguageForColumn($column_name) as $c) {
            $this->{$c['column']} = $request->get($c['column'], $default_value);
        }
    }
}
