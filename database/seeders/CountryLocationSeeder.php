<?php

namespace Database\Seeders;

use App\Models\Country;
use App\Models\CountryLocation;
use Illuminate\Database\Seeder;

class CountryLocationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        try {
            foreach ($this->getLocations() as $country_iso_2 => $var) {
                $country = Country::where('iso2', '=', $country_iso_2)->first();
                if ($country) {
                    foreach ($var as $key => $loc) {
                        $location = CountryLocation::where('country_id', '=', $country->id)
                            ->where('name_en', '=', $loc['name'])
                            ->first();

                        if (! $location) {
                            $location = new CountryLocation;
                            $location->country_id = $country->id;
                            foreach (loopLanguageForColumn('name') as $locale) {
                                $location->{$locale['column']} = $loc['name'];
                            }
                            $location->sorting = $key;
                            $location->save();
                        }
                    }
                }
            }

            Country::forgetCache();
        } catch (\Exception $e) {
            echo $e->getFile();
            echo "\n";
            echo $e->getLine();
            echo "\n";
            echo $e->getMessage();
            echo "\n";
            exit;
        }
    }

    public function getLocations(): array
    {
        $arr = [];

        $arr['my'] = [
            ['name' => 'WP Kuala Lumpur'],
            ['name' => 'Johor'],
            ['name' => 'Kedah'],
            ['name' => 'Kelantan'],
            ['name' => 'Melaka'],
            ['name' => 'Negeri Sembilan'],
            ['name' => 'Pahang'],
            ['name' => 'Penang'],
            ['name' => 'Perak'],
            ['name' => 'Perlis'],
            ['name' => 'Sabah'],
            ['name' => 'Sarawak'],
            ['name' => 'Selangor'],
            ['name' => 'Terengganu'],
            ['name' => 'WP Labuan'],
            ['name' => 'WP Putrajaya'],
        ];

        return $arr;
    }
}
