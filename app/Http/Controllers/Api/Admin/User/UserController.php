<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\AccountStatusLog;
use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function buildForm(Request $request)
    {
        return makeResponse(true, null, [
            'model' => \App\Models\User::with(['country', 'introducer'])->find($request->get('id')),
        ]);
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'username' => ['required', 'isUsername', 'unique:users,username,'.$request->get('id')],
            'email' => ['required', 'email', 'unique:users,email,'.$request->get('id')],
            'name' => ['required', 'string', 'min:1', 'max:24'],
            'country_id' => ['required', 'exists:country,id,status,1'],
            'contact_country_id' => ['required', 'exists:country,id,status,1'],
            'contact_number' => ['required', 'isContactNumber:'.$request->get('contact_country_id')],
        ];

        if (! $request->filled('id')) {
            $rules['introducer_username'] = ['required', 'isUsername', 'exists:users,username'];
            $rules['password'] = ['required', 'isPassword', 'confirmed'];
            $rules['password2'] = ['required', 'isPassword2', 'confirmed'];
        } else {
            $rules['new_password'] = ['nullable', 'isPassword', 'confirmed'];
            $rules['new_password2'] = ['nullable', 'isPassword2', 'confirmed'];
        }

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if (! $request->filled('id')) {
                $model = new User;

                $introducer = User::where('username', $request->get('introducer_username'))->first();

                if (! $introducer) {
                    throw new \Exception(__('x not found', ['x' => __('Introducer')]));
                }

                $model->introducer_user_id = $introducer->id;
            } else {
                $model = User::find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            }

            $model->username = $request->get('username');
            $model->email = $request->get('email');
            $model->name = $request->get('name');
            $model->country_id = $request->get('contact_country_id');
            $model->contact_country_id = $request->get('contact_country_id');
            $model->contact_number = $request->get('contact_number');
            if (! $request->filled('id')) {
                $model->password = $request->get('password');
                $model->password2 = $request->get('password2');
            } else {
                if ($request->filled('new_password')) {
                    $model->password = $request->get('new_password');
                }
                if ($request->filled('new_password2')) {
                    $model->password2 = $request->get('new_password2');
                }
            }

            $model->save();

            \DB::commit();

            return makeResponse(true, null, [
                'model' => $model,
            ]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function loginAccount(Request $request)
    {
        try {
            $model = User::find($request->get('id'));

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $token_result = $model->createToken('Personal Access Token');

            return makeResponse(true, null, ['token' => $token_result->plainTextToken]);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function toggleBanStatus(Request $request)
    {
        try {
            \DB::beginTransaction();

            $model = User::find($request->get('id'));

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $act = new AccountStatusLog;
            $act->admin_id = $request->user()->id;
            $act->user_id = $model->id;
            if ($model->ban == 0) {
                $act->operation = 'ban';
                $act->save();

                $model->ban = 1;
                $model->save();
            } else {
                $act->operation = 'unban';
                $act->save();

                $model->ban = 0;
                $model->save();
            }

            $model->tokens()->delete();

            \DB::commit();

            return makeResponse(true, null, ['model' => $model]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }

    public function loadStatistics(Request $request)
    {
        try {
            $data = [];
            $data['total_user'] = User::count();
            $data['total_credit_1'] = User::sum('credit_1');
            $data['total_credit_2'] = User::sum('credit_2');

            return makeResponse(true, null, ['statistics' => $data]);
        } catch (\Exception $e) {
            return makeResponse(false);
        }
    }

    public function checkUser(Request $request)
    {
        try {
            $model = User::where('username', $request->get('query'))->first();
            if ($model) {
                return makeResponse(true, null, ['name' => $model->name ?? $model->email ?? $model->username ?? $model->id]);
            } else {
                return makeResponse(true, __('Record not found'));
            }
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    //    public function delete(Request $request)
    //    {
    //        try {
    //            \DB::beginTransaction();
    //
    //            $model = null;
    //
    //            if ($request->filled('id')) {
    //                $model = Agent::find($request->get('id'));
    //            }
    //
    //            if (! $model) {
    //                throw new \Exception(__('Record not found'));
    //            }
    //
    //            $model->delete();
    //
    //            \DB::commit();
    //
    //            return makeResponse(true, null);
    //        } catch (\Exception $e) {
    //            \DB::rollBack();
    //
    //            return makeResponse(false, $e);
    //        }
    //    }
}
