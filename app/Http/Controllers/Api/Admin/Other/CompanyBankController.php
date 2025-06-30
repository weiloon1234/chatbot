<?php

namespace App\Http\Controllers\Api\Admin\Other;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\CompanyBank;
use Illuminate\Http\Request;

class CompanyBankController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = CompanyBank::find($request->get('id'));
            }

            $data['banks'] = Bank::with(['country'])
                ->orderBy('country_id', 'ASC')
                ->orderBy('name_en', 'ASC')->get();

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'bank_id' => ['required', 'exists:banks,id'],
            'account_name' => ['required', 'string', 'min:1', 'max:64'],
            'account_number' => ['required', 'string', 'min:1', 'max:64'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($request->filled('id')) {
                $model = CompanyBank::find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            } else {
                $model = new CompanyBank;
            }

            foreach ($rules as $column => $rule) {
                $model->{$column} = $request->get($column);
            }

            $bank = Bank::find($request->get('bank_id'));

            if (! $bank) {
                throw new \Exception(__('Record not found'));
            }

            foreach (loopLanguageForColumn('name') as $column) {
                $model->{$column['column']} = $bank->{$column['column']};
            }

            $model->country_id = $bank->country_id;

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

            $model = null;

            if ($request->filled('id')) {
                $model = CompanyBank::find($request->get('id'));
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
