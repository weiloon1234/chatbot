<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\AccountLoginLog;
use App\Models\Admin;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    public function fetch(Request $request)
    {
        $admin = $request->user();

        if (! $admin->last_login_at || ! $admin->last_login_at->isToday()) {
            try {
                \DB::beginTransaction();

                $log = new AccountLoginLog;
                $log->setAccount($admin);
                $log->login_type = 'auto_login';
                $log->save();

                $admin->last_login_at = $log->created_at;
                $admin->saveQuietly(); // TO DISABLE AUDIT LOG

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();

                return makeResponse(false, $e);
            }
        }

        return response()->json($request->user());
    }

    public function login(Request $request)
    {
        $this->validate($request, [
            'username' => ['required', 'isUsername'],
            'password' => ['required', 'isPassword'],
        ]);

        try {
            \DB::beginTransaction();

            $admin = Admin::where('username', '=', $request->get('username'))
                ->first();

            if (! $admin) {
                throw new \Exception(__('Incorrect credentials'));
            }

            if (! $admin->correctPassword($request->get('password'))) {
                return makeResponseErrorForField('password', __('Incorrect credentials'));
            }

            // SINGLE LOGIN
            //            $admin->tokens()->delete();

            $token_result = $admin->createToken('Personal Access Token');

            $log = new AccountLoginLog;
            $log->setAccount($admin);
            $log->login_type = 'login';
            $log->save();

            $admin->last_login_at = $log->created_at;
            $admin->saveQuietly(); // TO DISABLE AUDIT LOG

            \DB::commit();

            return makeResponse(true, null, [
                'token' => $token_result->plainTextToken,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function logout(Request $request)
    {
        if ($request->user()) {
            $request->user()->tokens()->delete();
        }

        session()->flush();

        return makeResponse(true, null);
    }

    public function changeLanguage(Request $request)
    {
        if ($request->user()) {
            $user = $request->user();
            $user->lang = $request->get('language');
            if (! array_key_exists($user->lang, config('app.locales'))) {
                $user->lang = config('app.fallback_locale');
            }
            $user->save();
        }

        return makeResponse(true, null);
    }

    public function profileSubmit(Request $request)
    {
        $me = $request->user();

        $rules = [
            'current_password' => ['required', 'isPassword'],
            'name' => ['nullable', 'string', 'min:1', 'max:64'],
            'email' => ['required', 'email', 'unique:admins,email,'.$me->id],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if (! $me->correctPassword($request->get('current_password'))) {
                throw new \Exception(__('Incorrect password'));
            }

            $me->name = $request->get('name');
            $me->email = $request->get('email');
            $me->save();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function securitySubmit(Request $request)
    {
        $me = $request->user();

        $rules = [
            'current_password' => ['required', 'isPassword'],
            'new_password' => ['nullable', 'isPassword', 'confirmed'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if (! $me->correctPassword($request->get('current_password'))) {
                throw new \Exception(__('Incorrect password'));
            }

            $me->password = $request->get('new_password');
            $me->save();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
