<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\WhatsappMessage;
use App\Services\WhatsappSocketService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class EtcController extends Controller
{
    public function loadLog(Request $request)
    {
        if ($request->user() && $request->user()->isDeveloper()) {
            $log = @file_get_contents(storage_path('logs/laravel.log'));

            return makeResponse(true, null, ['log' => $log !== false ? $log : '']);
        } else {
            return makeResponse(true, null, ['log' => __('Permission denied')]);
        }
    }

    public function clear(Request $request)
    {
        exec('rm -f '.storage_path('logs/*.log'));
        exec('rm -f '.storage_path('logs/*.gz'));
        Artisan::call('cache:clear');
        Artisan::call('config:clear');

        return makeResponse(true);
    }

    public function checkWhatsapp(Request $request)
    {
        $service = new WhatsappSocketService;

        return makeResponse(true, null, $service->checkOrStartSession());
    }

    public function checkWhatsappStatus(Request $request)
    {
        try {
            $service = new WhatsappSocketService;

            $active = $service->checkSession();
            if ($active) {
                return makeResponse(true, null, ['api_success' => true, 'active' => true, 'qr' => null]);
            }

            return makeResponse(true, null, ['api_success' => true, 'active' => false, 'qr' => null]);
        } catch (\Exception $e) {
            return makeResponse(false, null, ['api_success' => false, 'active' => false, 'qr' => null, 'message' => $e->getMessage()]);
        }
    }

    public function disconnectWhatsapp(Request $request)
    {
        $service = new WhatsappSocketService;

        return makeResponse(true, null, $service->disconnect());
    }

    public function sendMessageWhatsapp(Request $request)
    {
        $rules = [
            'contact_country_id' => ['required', 'exists:countries,id,status,1'],
            'contact_number' => ['required', 'isContactNumber:' . $request->get('contact_country_id')],
            'content' => ['required_without:files', 'nullable', 'string', 'max:5000'],
            'files' => ['required_without:content', 'array'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,pdf,doc,docx,zip', 'max:20480'],
        ];

        $this->validate($request, $rules);

        try {
            \DB::beginTransaction();

            $me = $request->user();

            $service = new WhatsappSocketService;
            $country = Country::Active()->find($request->get('contact_country_id'));

            $model = new WhatsappMessage;
            $model->session_id = $service->getSessionId();
            $model->admin_id = $me->id;
            $model->contact_country_id = $country->id;
            $model->contact_number = $request->get('contact_number');
            $model->message = $request->get('content');
            $model->save();

//            $poll = [
//                'name' => 'What is your favorite fruit?',
//                'options' => ['Apple', 'Banana', 'Mango']
//            ];
//
//            $resp = $service->sendMessage(
//                to: $model->full_contact_number,
//                poll: $poll
//            );

            $resp = $service->sendMessage(
                to: $model->full_contact_number,
                content: $model->message,
                files: $request->hasFile('files') ? $request->file('files') : null
            );

            if (! $resp['api_success']) {
                throw new \Exception($resp['api_message'] ?? __('Unknown error'));
            }

            \DB::commit();

            return makeResponse(true, null, $resp);
        } catch (\Exception $e) {
            return makeResponse(false, null, $e);
        }
    }
}
