<?php

namespace App\Http\Controllers\Api\Admin\Management;

use App\Http\Controllers\Controller;
use App\Models\Admin;
use App\Models\AdminGroup;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];

            $me = $request->user();

            if ($request->filled('id')) {
                if ($me->isDeveloper()) {
                    $data['model'] = Admin::with(['group'])->ListForDeveloper()->find($request->get('id'));
                } elseif ($me->isSuperAdmin()) {
                    $data['model'] = Admin::with(['group'])->ListForSuperAdmin()->find($request->get('id'));
                } else {
                    $data['model'] = Admin::with(['group'])->ListForAdmin()->find($request->get('id'));
                }
            }

            $data['groups'] = AdminGroup::orderBy('group_name', 'ASC')->get();

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $me = $request->user();

        $rules = [
            'username' => ['required', 'isUsername', 'unique:admins,username'],
            'email' => ['required', 'email', 'unique:admins,email'],
            'name' => ['required', 'string', 'min:1', 'max:64'],
        ];

        if ($request->filled('id')) {
            if ($me->isDeveloper()) {
                $model = Admin::ListForDeveloper()->find($request->get('id'));
            } elseif ($me->isSuperAdmin()) {
                $model = Admin::ListForSuperAdmin()->find($request->get('id'));
            } else {
                $model = Admin::ListForAdmin()->find($request->get('id'));
            }

            if (! $model) {
                return makeResponse(false, __('Record not found'));
            }

            $rules['username'] = ['required', 'isUsername', 'unique:admins,username,'.$model->id];
            $rules['email'] = ['required', 'email', 'unique:admins,email,'.$model->id];
            $rules['password'] = ['nullable', 'isPassword', 'confirmed'];
            if (! $model->isSuperAdmin()) {
                $rules['admin_group_id'] = ['required', 'exists:admin_groups,id'];
            }
        } else {
            $rules['password'] = ['required', 'isPassword', 'confirmed'];
            $rules['admin_group_id'] = ['required', 'exists:admin_groups,id'];

            $model = new Admin;
        }

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $model->username = $request->get('username');
            $model->name = $request->get('name');
            $model->email = $request->get('email');

            if (isset($rules['admin_group_id'])) {
                $model->admin_group_id = $request->get('admin_group_id');
            }

            if ($request->filled('password')) {
                $model->password = $request->get('password');
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

    public function delete(Request $request)
    {
        try {
            \DB::beginTransaction();

            if (! $request->filled('id')) {
                throw new \Exception(__('Record not found'));
            }

            $me = $request->user();

            if ($me->isDeveloper()) {
                $model = Admin::ListForDeveloper()->find($request->get('id'));
            } elseif ($me->isSuperAdmin()) {
                $model = Admin::ListForSuperAdmin()->find($request->get('id'));
            } else {
                $model = Admin::ListForAdmin()->find($request->get('id'));
            }

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $model->delete();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
