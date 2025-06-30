<?php

namespace App\Http\Controllers\Api\Admin\Other;

use App\Http\Controllers\Controller;
use App\Models\Bank;
use App\Models\Country;
use Illuminate\Http\Request;

class BankController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = Bank::find($request->get('id'));
            }

            $data['country'] = Country::where(function ($query) {
                $query->orWhere('status', 1)
                    ->orWhereHas('banks');
            })->orderBy('name', 'ASC')->get();

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'country_id' => ['required', 'exists:country,id'],
        ];

        foreach (loopLanguageForColumn('name') as $lang) {
            $rules[$lang['column']] = ['required', 'string', 'min:1', 'max:64'];
        }

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($request->filled('id')) {
                $model = Bank::find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            } else {
                $model = new Bank;
            }

            foreach ($rules as $column => $rule) {
                $model->{$column} = $request->get($column);
            }

            $model->country_id = $request->get('country_id');
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
                $model = Bank::find($request->get('id'));
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
