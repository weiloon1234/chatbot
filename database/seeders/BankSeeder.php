<?php

namespace Database\Seeders;

use App\Models\Bank;
use Illuminate\Database\Seeder;

class BankSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach ($this->getBanks() as $var) {
            $count = Bank::where('name_en', '=', $var['name_en'])->where('country_id', '=', $var['country_id'])->count();
            if (! $count) {
                $c = new Bank;

                foreach ($var as $k => $v) {
                    $c->$k = $v;
                }

                $c->save();
            }
        }
    }

    public function getBanks(): array
    {
        $arr = [];

        $my = \App\Models\Country::where('iso2', '=', 'MY')->first();
        $cn = \App\Models\Country::where('iso2', '=', 'CN')->first();
        if ($my) {
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Alliance Bank Malaysia Berhad', 'name_zh' => '安联银行 （ Alliance Bank）'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Affin Bank Berhad (Affin Bank)', 'name_zh' => '艾芬银行 (Affin Bank)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'AMMB Holdings Berhad (AmBank)', 'name_zh' => '大马银行 （AmBank）'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'HSBC Bank Malaysia Berhad (HSBC)', 'name_zh' => '汇丰银行 (HSBC)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'OCBC Bank (Malaysia) Berhad (OCBC)', 'name_zh' => '華僑銀行 (OCBC)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Rashid Hussein Bank (RHB)', 'name_zh' => '兴业银行 (RHB)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Bank Simpanan Malaysia (BSN)', 'name_zh' => '国民储蓄银行 (BSN)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'United Overseas Bank (UOB)', 'name_zh' => '大华银行 (UOB)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Hong Leong Bank Berhad (HLBB)', 'name_zh' => '丰隆银行 (Hong Leong Bank)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Bank Islam Malaysia Berhad (BIMB)', 'name_zh' => '马来西亚伊斯兰银行 (Bank Islam)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'CIMB Bank Berhad (CIMB)', 'name_zh' => '联昌银行 (CIMB Bank)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Public Bank Berhad (PBE)', 'name_zh' => '大众银行 (Public Bank)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Standard Chartered Bank Malaysia Berhad (SCB)', 'name_zh' => '渣打银行 (Standard Chartered Bank)'];
            $arr[] = ['country_id' => $my->id, 'name_en' => 'Malayan Banking Berhad (MBB)', 'name_zh' => '马来亚银行 (Maybank)'];
        }

        if ($cn) {
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国银行', 'name_zh' => '中国银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国农业银行', 'name_zh' => '中国农业银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国招商银行', 'name_zh' => '中国招商银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国工商银行', 'name_zh' => '中国工商银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国民生银行', 'name_zh' => '中国民生银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国交通银行', 'name_zh' => '中国交通银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国建设银行', 'name_zh' => '中国建设银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '兴业银行', 'name_zh' => '兴业银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国邮政储蓄银行', 'name_zh' => '中国邮政储蓄银行'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '中国信合', 'name_zh' => '中国信合'];
            $arr[] = ['country_id' => $cn->id, 'name_en' => '深圳农村商业银行', 'name_zh' => '深圳农村商业银行'];
        }

        return $arr;
    }
}
