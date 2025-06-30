<?php

namespace App\Http\Controllers\Api\Admin\Other;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function buildForm(Request $request)
    {
        return makeResponse(true, null, [
            'model' => Setting::find($request->get('id')),
        ]);
    }

    public function submitForm(Request $request)
    {
        $this->validate($request, [
            'id' => ['required', 'exists:settings,id'],
            'setting_value' => ['required'],
        ]);

        try {
            \DB::beginTransaction();

            $model = Setting::find($request->get('id'));

            if (! $model) {
                throw new \Exception(__('Record not found'));
            }

            if ($model->setting_type == 'editor') {
                $model->setting_value = json_encode($request->get('setting_value'));
            } elseif ($model->setting_type == 'image') {
                if ($request->hasFile('setting_value')) {
                    $request->validate([
                        'setting_value' => ['required', 'image'],
                    ]);
                    $file = $request->file('setting_value');

                    $img = \Image::make($file);

                    $path = getUploadFileBasePath('setting').'/whats_new_banner.jpg';

                    getUploadStorage()->put($path, $img->stream('jpg', 90));

                    $model->setting_value = $path.'?t='.now()->timestamp;
                }
            } else {
                $model->setting_value = $request->get('setting_value');
            }
            $model->save();

            \DB::commit();

            return makeResponse(true, null, ['model' => $model]);
        } catch (\Exception $e) {
            \DB::rollBack();

            return makeResponse(false, $e);
        }
    }
}
