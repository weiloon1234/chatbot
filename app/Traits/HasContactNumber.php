<?php

namespace App\Traits;

use App\Models\Country;
use App\Services\PhoneService;

trait HasContactNumber
{
    public function contactCountry()
    {
        return $this->hasOne(Country::class, 'id', 'contact_country_id');
    }

    public function getContactNumber()
    {
        return PhoneService::formatE164($this->contact_number, $this->contactCountry);
    }

    public function setContactNumberAttribute($value)
    {
        $this->attributes['contact_number'] = preg_replace('/\D/', '', $value);
        $this->buildFullContactNumber();
    }

    public function setContactCountryIdAttribute($value)
    {
        $this->attributes['contact_country_id'] = $value;
        $this->buildFullContactNumber();
    }

    public function buildFullContactNumber()
    {
        if ($this->contact_number && $this->contactCountry) {
            $formatted = PhoneService::formatE164($this->contact_number, $this->contactCountry);
            if ($formatted) {
                $this->full_contact_number = $formatted;
            } else {
                $this->full_contact_number = $this->contact_number;
            }
        } else {
            $this->full_contact_number = $this->contact_number;
        }
    }
}
