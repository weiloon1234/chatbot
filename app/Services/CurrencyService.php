<?php

namespace App\Services;

use App\Config;
use App\Models\Country;

class CurrencyService
{
    public static function formatAmount(Country $country, $amount, $withSymbol = true)
    {
        $decimal = $country->currency_decimal ?? Config::DECIMAL_POINT;
        $formatted = number_format($amount, $decimal);

        if (! $withSymbol) {
            return $formatted;
        }

        $prefix = $country->currency_symbol_prefix ?: '';
        $suffix = $country->currency_symbol_suffix ?: '';

        return trim("{$prefix}{$formatted}{$suffix}");
    }

    /**
     * Convert the amount from one country's currency to another using rate_to_base logic.
     *
     * @param  float|int  $amount
     * @return float
     */
    public static function convertToBase(Country $country, $amount)
    {
        if (! $country->rate_to_base || $country->rate_to_base == 0) {
            throw new \InvalidArgumentException("Invalid rate for {$country->name}");
        }

        return $amount * $country->rate_to_base;
    }

    /**
     * Convert the amount from one country's currency to another using rate_to_base logic.
     *
     * @param  float|int  $amount
     * @return float
     *
     * @throws \InvalidArgumentException
     */
    public static function convertBetween(Country $from, Country $to, $amount)
    {
        $baseAmount = self::convertToBase($from, $amount);
        if (! $to->rate_to_base || $to->rate_to_base == 0) {
            throw new \InvalidArgumentException("Invalid rate for {$to->name}");
        }

        return $baseAmount / $to->rate_to_base;
    }
}
