<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountLoginLog;
use App\Models\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        try {
            \DB::beginTransaction();

            $new = [];
            $login = [];

            $now = now();
            $start = now()->startOfYear();
            $end = now()->endOfYear();

            $period = CarbonPeriod::create($start, '1 month', $end);

            foreach ($period as $dt) {
                $s = $dt->copy()->startOfMonth();
                $e = $dt->copy()->endOfMonth();

                $new[] = $this->getRegisterData($s, $e, $now->copy()->format('Y-m') != $dt->copy()->format('Y-m'));
                $login[] = $this->getLoginData($s, $e, $now->copy()->format('Y-m') != $dt->copy()->format('Y-m'));
            }

            \DB::commit();

            return makeResponse(true, null, [
                'new' => $new,
                'login' => $login,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    protected function getRegisterData(Carbon $from, Carbon $to, $can_cache)
    {
        $data = null;
        $cache_key = 'admin_register_data_'.$from->copy()->format('Y_m');

        if ($can_cache) {
            $data = cache()->get($cache_key);
        }

        if (($can_cache && $data === null) || ! $can_cache) {
            $count = User::where('created_at', '>=', $from)->where('created_at', '<=', $to)->count();

            if ($can_cache && $data === null) {
                cache()->rememberForever($cache_key, function () use ($count) {
                    return $count;
                });
            }

            $data = $count;
        }

        return (int) $data;
    }

    protected function getLoginData($from, $to, $can_cache)
    {
        $data = null;
        $cache_key = 'admin_login_data_'.$from->copy()->format('Y_m');

        if ($can_cache) {
            $data = cache()->get($cache_key);
        }

        if (($can_cache && $data === null) || ! $can_cache) {
            $count = AccountLoginLog::whereRaw("`model` = '".getClass(new User, true)."'")
                ->where('created_at', '>=', $from)->where('created_at', '<=', $to)->count();

            if ($can_cache && $data === null) {
                cache()->rememberForever($cache_key, function () use ($count) {
                    return $count;
                });
            }

            $data = $count;
        }

        return (int) $data;
    }
}
