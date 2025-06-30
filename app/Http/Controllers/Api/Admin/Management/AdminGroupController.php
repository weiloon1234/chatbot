<?php

namespace App\Http\Controllers\Api\Admin\Management;

use App\Http\Controllers\Controller;
use App\Models\AdminGroup;
use App\Models\AdminGroupPermission;
use Illuminate\Http\Request;

class AdminGroupController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = AdminGroup::with(['permissions'])->find($request->get('id'));
            }

            $data['permissions'] = AdminGroupPermission::getPermissionTagLists();

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'group_name' => ['required', 'string', 'min:1', 'max:64'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($request->filled('id')) {
                $model = AdminGroup::find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            } else {
                $model = new AdminGroup;
            }

            foreach ($rules as $column => $rule) {
                $model->{$column} = $request->get($column);
            }

            $selected_count = 0;
            $permission_lists = AdminGroupPermission::getPermissionTagLists();
            if (! $request->filled('permissions')) {
                throw new \Exception(__('Please select at least x', ['x' => 1 .__('Permission')]));
            }

            foreach ($request->get('permissions') as $pm => $value) {
                if ($value === 'yes' && array_key_exists($pm, $permission_lists)) {
                    $selected_count++;
                }
            }

            if ($selected_count <= 0) {
                throw new \Exception(__('Please select at least x', ['x' => 1]));
            }

            if ($model->exists && $model->permissions) {
                $model->permissions()->delete();
            }

            $model->save();

            foreach ($request->get('permissions') as $pm => $value) {
                if ($value === 'yes' && array_key_exists($pm, $permission_lists)) {
                    $p = new AdminGroupPermission;
                    $p->admin_group_id = $model->id;
                    $p->permission_tag = $pm;
                    $p->save();
                }
            }

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

            $model = null;

            if ($request->filled('id')) {
                $model = AdminGroup::find($request->get('id'));
            }

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $model->permissions()->delete();
            $model->delete();

            \DB::commit();

            return makeResponse(true, null);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
