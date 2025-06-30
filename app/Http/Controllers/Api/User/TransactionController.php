<?php

namespace App\Http\Controllers\Api\User;

use App\Http\Controllers\Controller;
use App\Managers\Calculator;
use App\Models\Bank;
use App\Models\User;
use App\Models\UserCreditTransaction;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    protected $role = 'user';

    protected $withdraw_admin_fees_percentage = [
        1 => 10,
    ];

    protected $min_withdraw_amount = [
        1 => 10,
    ];

    public function loadData(Request $request)
    {
        $me = $request->user();
        $models = UserCreditTransaction::where('user_id', '=', $me->id)->orderBy('id', 'DESC');
        if (in_array($request->get('cid'), [1, 2])) {
            $models = $models->where('credit_type', '=', $request->get('cid'));
        } else {
            return makeResponse(false, null);
        }

        $groups = [
            1 => [3], // Deposit
            2 => [201, 301, 401], // Spending
            11 => [361, 461], // Cashback
            20 => [231, 251], // Subscription bonus
            21 => [362], // Agent bonus
            22 => [363], // Manager bonus
            23 => [364], // Matching bonus
            24 => [365], // Merchant introducer bonus
            999 => [],
        ];

        foreach ($groups as $key => $g) {
            if ($key != 999) {
                $groups[999] = array_merge($groups[999], $g);
            }
        }

        if ($request->filled('group') && $request->get('group') > 0) {
            if ($request->get('group') != 999) {
                $models = $models->whereIn('transaction_type', $groups[$request->get('group')]);
            } else {
                $models = $models->whereNotIn('transaction_type', $groups[$request->get('group')]);
            }
        }

        return response()->json($models->paginate());
    }

    public function summary(Request $request)
    {
        $me = $request->user();
        $data = [
            'min_withdraw_amount' => $this->min_withdraw_amount,
            'last_withdraw_bank' => Withdrawal::where('role', '=', 'user')->where('user_id', '=', $me->id)->where('withdraw_method', '=', 99)->orderBy('id', 'DESC')->first(),
            'admin_fees_percentage' => $this->withdraw_admin_fees_percentage,
            'banks' => Bank::where('country_id', '=', $me->country_id)->orderBy('name_en', 'ASC')->get(),
            'cryptos' => Withdrawal::getCryptoTypeLists(),
            'country' => $me->country,
        ];

        $cryptos = [];
        foreach (Withdrawal::getCryptoTypeLists() as $key => $value) {
            $w = Withdrawal::where('role', '=', 'user')
                ->where('user_id', '=', $me->id)
                ->where('withdraw_method', '=', 98)
                ->where('crypto_type', '=', $key)
                ->orderBy('id', 'DESC')
                ->first();
            if ($w) {
                $cryptos[$key] = $w->crypto_address;
            }
        }

        $data['last_withdraw_cryptos'] = $cryptos;

        return makeResponse(true, null, $data);
    }

    public function withdrawSubmitForm(Request $request)
    {
        $me = $request->user();

        $rules = [
            'amount' => ['required', 'isFund'],
            'current_password2' => ['required', 'isPassword2'],
        ];

        if ($request->get('withdraw_method') == 98) {// Cryptocurrency
            $rules['crypto_type'] = ['required', 'in:'.implode(',', array_keys(Withdrawal::getCryptoTypeLists()))];
            $rules['crypto_address'] = ['required', 'string', 'min:12'];
        } elseif ($request->get('withdraw_method') == 99) {// TO BANK
            $rules['bank_id'] = ['required', 'exists:banks,id,country_id,'.$me->country_id];
            $rules['bank_account_holder_name'] = ['required', 'string', 'min:1', 'max:64'];
            $rules['bank_account_number'] = ['required', 'string', 'min:1', 'max:64'];
        } else {
            return makeResponse(false, __('Unknown error'));
        }

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if (! $me->correctPassword2($request->get('current_password2'))) {
                throw new \Exception(__('Incorrect password2'));
            }

            $withdraw_method = $request->get('withdraw_method');

            $cid = 1;
            $cfield = 'credit_'.$cid;
            $credit_amount = abs($request->get('amount', 0));

            if ($credit_amount < $this->min_withdraw_amount[$cid]) {
                throw new \Exception(__('Minimum withdraw x', ['x' => __(fundFormat($this->min_withdraw_amount[$cid])).' '.__('Credit '.$cid.' short')]));
            }

            if ($me->{$cfield} < $credit_amount) {
                throw new \Exception(__('Insufficient credit '.$cid));
            }

            if ($withdraw_method == 99) {// BANK
                $conversion_rate = $me->country->conversion_rate;
            } else {
                $conversion_rate = 1;
            }

            $credit_value = $credit_amount * $conversion_rate;

            $rk = \Str::uuid();

            $admin_fees_amount = $this->withdraw_admin_fees_percentage[$cid] > 0 ? (new Calculator($credit_amount))->percentage($this->withdraw_admin_fees_percentage[$cid])->getAnswer() : 0;

            $params = [
                'admin_fees_percentage' => $this->withdraw_admin_fees_percentage[$cid],
                'credit_amount' => $credit_amount,
                'admin_fees_amount' => $admin_fees_amount,
                'conversion_rate' => $conversion_rate,
                'credit_value' => $credit_value,
            ];

            $w = new Withdrawal;
            $w->role = $this->role;
            $w->user_id = $me->id;
            $w->credit_type = $cid;
            $w->credit_amount = $credit_amount;
            $w->admin_fees = $admin_fees_amount;
            $w->country_id = $me->country_id;
            $w->conversion_rate = $me->country->conversion_rate;
            $w->receivable_currency_amount = (new Calculator($credit_amount))->minus($admin_fees_amount)->times($w->conversion_rate)->getAnswer();
            $w->withdraw_method = $withdraw_method;
            if ($w->withdraw_method == 98) {
                $w->crypto_type = $request->get('crypto_type');
                $w->crypto_address = $request->get('crypto_address');
            } elseif ($w->withdraw_method == 99) {
                $w->bank_id = $request->get('bank_id');
                $w->bank_account_holder_name = $request->get('bank_account_holder_name');
                $w->bank_account_number = $request->get('bank_account_number');
            }
            $w->status = $w->withdraw_method == 1 ? 1 : 0;
            $w->related_key = $rk;
            $w->params = $params;
            $w->save();

            $ut = new UserCreditTransaction;
            $ut->user_id = $me->id;
            $ut->credit_type = $cid;
            $ut->transaction_type = 21;
            $ut->amount = 0 - $credit_amount;
            $ut->related_key = $rk;
            if ($w->withdraw_method == 98) {
                $t_params = array_merge($params);

                $t_params['withdraw_id'] = $w->id;
                $t_params['crypto_type'] = __('Crypto type '.$w->crypto_type, [], 'en');
                $t_params['crypto_address'] = $w->crypto_address;
                $t_params['crypto_amount'] = $w->receivable_currency_amount;

                $ut->params = $t_params;
            } elseif ($w->withdraw_method == 99) {
                $bank = Bank::find($w->bank_id);

                $t_params = array_merge($params);
                $t_params['withdraw_id'] = $w->id;

                foreach (config('app.locales') as $locale => $text) {
                    $t_params['bank_name_'.$locale] = $bank->{'name_'.$locale};
                    $t_params['bank_account_holder_name'] = $w->bank_account_holder_name;
                    $t_params['bank_account_number'] = $w->bank_account_number;
                    $t_params['currency_amount'] = $w->receivable_currency_amount;
                    $t_params['currency_prefix'] = $w->country->currency_prefix;
                }

                $ut->params = $t_params;
            }
            $ut->save();

            User::where('id', '=', $me->id)
                ->decrement('credit_'.$ut->credit_type, $credit_amount);

            \DB::commit();

            return makeResponse(true);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
