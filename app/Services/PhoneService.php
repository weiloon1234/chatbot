<?php

namespace App\Services;

use App\Models\Country;
use libphonenumber\NumberParseException;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

class PhoneService
{
    /**
     * Format a phone number to E.164 format.
     *
     * @return string|false
     */
    public static function formatE164(string $number, ?Country $country = null)
    {
        if (! $country) {
            return false;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $proto = $phoneUtil->parse($number, $country->iso2);
            if ($phoneUtil->isValidNumber($proto)) {
                return $phoneUtil->format($proto, PhoneNumberFormat::E164);
            }
        } catch (NumberParseException $e) {
            return false;
        }

        return false;
    }

    /**
     * Format a phone number to International format.
     *
     * @return string|false
     */
    public static function formatInternational(Country $country, string $number)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $proto = $phoneUtil->parse($number, $country->iso2);
            if ($phoneUtil->isValidNumber($proto)) {
                return $phoneUtil->format($proto, PhoneNumberFormat::INTERNATIONAL);
            }
        } catch (NumberParseException $e) {
            return false;
        }

        return false;
    }

    /**
     * Format a phone number to National format.
     *
     * @return string|false
     */
    public static function formatNational(Country $country, string $number)
    {
        $phoneUtil = PhoneNumberUtil::getInstance();
        try {
            $proto = $phoneUtil->parse($number, $country->iso2);
            if ($phoneUtil->isValidNumber($proto)) {
                return $phoneUtil->getNationalSignificantNumber($proto);
            }
        } catch (NumberParseException $e) {
            return false;
        }

        return false;
    }

    public static function validate(?string $number, ?Country $country = null)
    {
        if (! $number || ! $country) {
            return false;
        }

        $phoneUtil = PhoneNumberUtil::getInstance();

        try {
            $proto = $phoneUtil->parse($number, $country->iso2);

            return $phoneUtil->isValidNumber($proto);
        } catch (NumberParseException $e) {
            return false;
        }
    }
}
