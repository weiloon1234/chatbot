<?php

namespace App\Http\Controllers\Api\Admin\User;

use App\Http\Controllers\Controller;
use App\Models\AdminAdjustUserCredit;
use App\Models\User;
use App\Models\UserCreditTransaction;
use Illuminate\Http\Request;

class UserCreditController extends Controller
{
    public function submitForm(Request $request)
    {
        $rules = [
            'username' => ['required', 'isUsername', 'exists:users,username'],
            'transaction_type' => ['required', 'in:'.implode(',', array_keys(AdminAdjustUserCredit::getTransactionTypeLists()))],
            'credit_type' => ['required', 'in:1,2'],
            'amount' => ['required', 'isFund'],
            'remark' => ['nullable', 'string'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $me = $request->user();

            $user = User::where('username', '=', $request->get('username'))->first();
            if (! $user) {
                throw new \Exception(__('x not found', ['x' => __('User')]));
            }

            $amount = abs($request->get('amount', 0));

            $rk = \Str::uuid();

            $adjust = new AdminAdjustUserCredit;
            $adjust->user_id = $user->id;
            $adjust->admin_id = $me->id;
            $adjust->transaction_type = $request->get('transaction_type');
            $adjust->credit_type = $request->get('credit_type');
            $adjust->amount = $amount;
            $adjust->remark = $request->get('remark');
            $adjust->related_key = $rk;
            $adjust->save();

            $ut = new UserCreditTransaction;
            $ut->user_id = $user->id;
            $ut->credit_type = $adjust->credit_type;
            $ut->transaction_type = $adjust->transaction_type;
            $ut->related_key = $adjust->related_key;

            if ($adjust->transaction_type == 1) {
                // Add
                $ut->amount = $amount;
                $ut->save();

                User::where('id', '=', $user->id)->increment('credit_'.$ut->credit_type, $amount);
            } elseif ($adjust->transaction_type == 2) {
                $ut->amount = 0 - $amount;
                $ut->save();

                User::where('id', '=', $user->id)->decrement('credit_'.$ut->credit_type, $amount);
            } else {
                throw new \Exception(__('Unknown error'));
            }

            \DB::commit();

            return makeResponse(true, null, ['model' => $adjust]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
