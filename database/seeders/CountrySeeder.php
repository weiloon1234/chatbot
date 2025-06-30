<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class CountrySeeder extends Seeder
{
    protected function getCountriesJsonPath(): string
    {
        return storage_path('app/countries_cleaned.json');
    }

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        if (! file_exists($this->getCountriesJsonPath())) {
            $this->buildCorrectCountryData();
        }

        $countries = json_decode(file_get_contents($this->getCountriesJsonPath()), true);
        $assignable = [
            'iso2', 'iso3', 'name', 'phone_code', 'currency_code',
            'currency_symbol_prefix', 'currency_symbol_suffix',
            'currency_decimal', 'rate_to_base', 'status',
        ];

        foreach ($countries as $country) {
            $c = \App\Models\Country::where('iso2', '=', $country['iso2'])
                ->first();

            if (! $c) {
                $c = new \App\Models\Country;
                foreach ($assignable as $field) {
                    $c->$field = $country[$field] ?? null;
                }
                if ($c->iso2 == \App\Config::DEFAULT_COUNTRY_CODE) {
                    $c->status = 1;
                    $c->rate_to_base = 1;
                }
                $c->save();
            }
        }
    }

    protected function buildCorrectCountryData()
    {
        $apiDataJson = file_get_contents('https://restcountries.com/v3.1/all?fields=cca2,cca3,name,idd,currencies,independent,unMember');
        $apiData = json_decode($apiDataJson, true);

        // Build API map
        $apiMap = [];
        foreach ($apiData as $country) {
            if (empty($country['independent']) && empty($country['unMember'])) {
                continue;
            }

            $iso2 = $country['cca2'];
            $iso3 = $country['cca3'];
            $phone_code = null;
            if (
                isset($country['idd']['root']) &&
                isset($country['idd']['suffixes']) &&
                is_array($country['idd']['suffixes']) &&
                ! empty($country['idd']['suffixes'])
            ) {
                $phone_code = $country['idd']['root'].$country['idd']['suffixes'][0];
            }
            $currency_code = isset($country['currencies']) ? array_key_first($country['currencies']) : null;
            $currency_symbol = null;
            if (isset($country['currencies']) && is_array($country['currencies'])) {
                $currencyData = reset($country['currencies']);
                if (is_array($currencyData) && isset($currencyData['symbol'])) {
                    $currency_symbol = $currencyData['symbol'];
                }
            }

            $apiMap[$iso2] = [
                'iso2' => $iso2,
                'iso3' => $iso3,
                'name' => $country['name']['common'],
                'phone_code' => $phone_code ? ltrim($phone_code, '+') : null,
                'currency_code' => $currency_code,
                'currency_symbol_prefix' => null,
                'currency_symbol_suffix' => $currency_symbol,
                'currency_decimal' => 2,
                'rate_to_base' => null,
                'status' => 0,
            ];
        }

        $finalData = [];

        // Load local country list if it exists
        $existingCountries = [];
        if (file_exists($this->getCountriesJsonPath())) {
            $existingCountries = json_decode(file_get_contents($this->getCountriesJsonPath()), true);
        }

        // Adjust existing countries
        foreach ($existingCountries as $my) {
            $iso2 = $my['iso2'];
            if (isset($apiMap[$iso2])) {
                $api = $apiMap[$iso2];

                $finalData[] = [
                    'iso2' => $api['iso2'],
                    'iso3' => $api['iso3'],
                    'name' => $api['name'],
                    'phone_code' => $api['phone_code'],
                    'currency_code' => $api['currency_code'],
                    'currency_symbol_prefix' => null,
                    'currency_symbol_suffix' => $api['currency_symbol_suffix'],
                    'currency_decimal' => 2,
                    'rate_to_base' => null,
                    'status' => $my['status'] ?? 0,
                ];

                unset($apiMap[$iso2]);
            }
        }

        // Add missing countries
        foreach ($apiMap as $iso2 => $api) {
            $finalData[] = [
                'iso2' => $api['iso2'],
                'iso3' => $api['iso3'],
                'name' => $api['name'],
                'phone_code' => $api['phone_code'],
                'currency_code' => $api['currency_code'],
                'currency_symbol_prefix' => null,
                'currency_symbol_suffix' => $api['currency_symbol_suffix'],
                'currency_decimal' => 2,
                'rate_to_base' => null,
                'status' => 0,
            ];
        }

        usort($finalData, function ($a, $b) {
            return strcasecmp($a['name'], $b['name']);
        });

        // Save to JSON
        $json = json_encode($finalData, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        file_put_contents($this->getCountriesJsonPath(), $json);

        echo "✅ Cleaned country data written to storage/app/countries_cleaned.json\n";
    }

    protected function countryLists(): array
    {
        return json_decode(file_get_contents(storage_path('app/countries_cleaned.json')), true);
    }
}
