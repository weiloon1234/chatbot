<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Models\AccountLoginLog;
use App\Models\User;
use Illuminate\Http\Request;

class AccountController extends Controller
{
    protected $login_identity = 'username';

    public function login(Request $request)
    {
        $rules = [
            'password' => ['required', 'isPassword'],
        ];

        if ($this->login_identity == 'email') {
            $rules['email'] = ['required', 'email', 'exists:users,email'];
        } elseif ($this->login_identity === 'contact') {
            $rules['contact_country_id'] = ['required', 'integer', 'exists:country,id,status,1'];
            $rules['contact_number'] = ['required', 'isContactNumber'];
        } else {
            $rules['username'] = ['required', 'isUsername', 'exists:users,username'];
        }

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($this->login_identity == 'contact') {
                $user = User::where('contact_country_id', '=', $request->get('contact_country_id'))
                    ->where('contact_number', '=', $request->get('contact_number'))
                    ->first();
            } else {
                $user = User::where($this->login_identity, '=', $request->get($this->login_identity))
                    ->first();
            }

            if (! $user) {
                throw new \Exception(__('Incorrect credentials'));
            }

            if (! $user->correctPassword($request->get('password'))) {
                return makeResponseErrorForField('password', __('Incorrect credentials'));
            }

            if ($user->ban == 1) {
                throw new \Exception(__('Account banned'));
            }

            // SINGLE LOGIN
            $user->tokens()->delete();

            $token_result = $user->createToken('Personal Access Token');

            $log = new AccountLoginLog;
            $log->setAccount($user);
            $log->login_type = 'login';
            $log->save();

            $user->last_login_at = $log->created_at;
            $user->save();

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

    public function fetch(Request $request)
    {
        $user = $request->user();

        if ($user && $user->ban == 1) {
            return makeResponse(false, __('Account banned'));
        }

        if (! $user->last_login_at || ! $user->last_login_at->isToday()) {
            try {
                \DB::beginTransaction();

                $log = new AccountLoginLog;
                $log->setAccount($user);
                $log->login_type = 'auto_login';
                $log->save();

                $user->last_login_at = $log->created_at;
                $user->save();

                \DB::commit();
            } catch (\Exception $e) {
                \DB::rollBack();

                return makeResponse(false, $e);
            }
        }

        return response()->json($request->user());
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

    public function changeAvatar(Request $request)
    {
        $this->validate($request, [
            'logo' => ['required', 'image'],
        ]);

        try {
            \DB::beginTransaction();

            $me = $request->user();
            if (! $request->hasFile('logo')) {
                throw new \Exception(__('Unknown error'));
            }

            $me->avatar = $request->file('logo');
            $me->save();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function manageMyProfile(Request $request)
    {
        $me = $request->user();

        $rules = [
            //            'email' => ['required', 'email', 'unique:users,email,'.$me->id],
            'name' => ['required', 'string', 'min:1', 'max:46'],
            'contact_country_id' => ['required', 'exists:country,id,status,1'],
            'contact_number' => ['required', 'isContactNumber'],
            'current_password2' => ['required', 'isPassword2'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if (! $me->correctPassword2($request->get('current_password2'))) {
                return makeResponseErrorForField('current_password2', __('Incorrect password2'));
            }

            //            $me->email = $request->get('email');
            $me->country_id = $request->get('contact_country_id');
            $me->contact_country_id = $request->get('contact_country_id');
            $me->contact_number = $request->get('contact_number');
            $me->name = $request->get('name');
            $me->save();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function changePassword(Request $request)
    {
        $rules = [
            'new_password' => ['nullable', 'isPassword', 'confirmed'],
            'new_password_confirmation' => ['nullable', 'isPassword'],
            'new_password2' => ['nullable', 'isPassword2', 'confirmed'],
            'new_password_confirmation2' => ['nullable', 'isPassword2'],
            'current_password2' => ['required', 'isPassword2'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $me = $request->user();
            if (! $me->correctPassword2($request->get('current_password2'))) {
                return makeResponseErrorForField('current_password2', __('Incorrect password2'));
            }

            if ($request->filled('new_password')) {
                $me->password = $request->get('new_password');
            }
            if ($request->filled('new_password2')) {
                $me->password2 = $request->get('new_password2');
            }
            $me->save();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
