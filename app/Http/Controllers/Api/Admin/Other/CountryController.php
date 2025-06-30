<?php

namespace App\Http\Controllers\Api\Admin\Other;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = Country::find($request->get('id'));
            }

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'id' => ['required', 'exists:countries,id'],
            'rate_to_base' => ['nullable', 'isFund'],
            'status' => ['required', 'in:'.implode(',', array_keys(Country::getStatusLists()))],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $model = Country::find($request->get('id'));

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            $model->rate_to_base = $request->get('rate_to_base');
            $model->status = $request->get('status', 0);
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
