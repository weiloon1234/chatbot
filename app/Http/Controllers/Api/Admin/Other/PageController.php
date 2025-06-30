<?php

namespace App\Http\Controllers\Api\Admin\Other;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\Request;

class PageController extends Controller
{
    public function getModel()
    {
        return new Page;
    }

    public function buildForm(Request $request)
    {
        try {
            $data = [];
            if ($request->filled('id')) {
                $data['model'] = $this->getModel()->find($request->get('id'));
            }

            return makeResponse(true, null, $data);
        } catch (\Exception $e) {
            return makeResponse(false, $e);
        }
    }

    public function submitForm(Request $request)
    {
        $rules = [
            'title' => ['required', 'string', 'min:1', 'max:64'],
        ];

        if (! $request->filled('id')) {
            $rules['title'] = ['required', 'string', 'min:1', 'max:64'];
        }

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            if ($request->filled('id')) {
                $model = $this->getModel()->find($request->get('id'));

                if (! $model) {
                    throw new \Exception(__('Record not found'));
                }
            } else {
                $model = new Page;
                $model->is_system = 0;
            }

            if ($model->is_system != 1) {
                $model->title = $request->get('title');
                if (! $model->title) {
                    return makeResponseErrorForField('title', __('Please key in x', ['x' => __('Title')]));
                }
            }

            foreach (loopLanguageForColumn('content') as $locale) {
                $model->{$locale['column']} = $request->get($locale['column']);
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

            $model = Page::find($request->get('id'));
            if (! $model) {
                throw new \Exception(__('Record not found'));
            }
            if ($model->is_system == 1) {
                throw new \Exception(__('Permission denied'));
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
