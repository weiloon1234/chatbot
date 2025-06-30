<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class WithdrawalController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = Withdrawal::with(['user', 'bank', 'country'])->find($request->get('id'));
            }

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:withdrawals,id'],
            'status' => ['required', 'in:'.implode(',', array_keys(Withdrawal::getStatusLists()))],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $model = Withdrawal::find($request->get('id'));

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $model->admin_id = $request->user()->id;
            if ($request->get('status') == 2) {
                $model->markCanceled();
            } else {
                $model->status = $request->get('status');
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
}
