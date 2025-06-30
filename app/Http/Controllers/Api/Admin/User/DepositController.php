<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\Deposit;
use Illuminate\Http\Request;

class DepositController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = Deposit::with(['receipts', 'user', 'bank', 'country'])->find($request->get('id'));
            }

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:deposits,id'],
            'status' => ['required', 'in:'.implode(',', array_keys(Deposit::getStatusLists()))],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $model = Deposit::find($request->get('id'));

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $model->admin_id = $request->user()->id;
            if ($request->get('status') == 1) {
                $model->markCompleted();
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
