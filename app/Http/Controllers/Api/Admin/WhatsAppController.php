<?php

namespace App\Http\Controllers\Api\Admin;

use App\Http\Controllers\Controller;
use App\Models\BotMessage;
use App\Models\Country;
use App\Services\PhoneService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;

class WhatsAppController extends Controller
{
    protected $whatsAppService;

    public function __construct(WhatsAppService $whatsAppService)
    {
        $this->whatsAppService = $whatsAppService;
    }

    public function startSession(Request $request)
    {
        $response = $this->whatsAppService->startSession(config('env.WHATSAPP_SESSION_ID'));

        return makeResponse($response->status(), null, $response->json());
    }

    public function terminateSession(Request $request)
    {
        $response = $this->whatsAppService->terminateSession(config('env.WHATSAPP_SESSION_ID'));

        return makeResponse($response->status(), null, $response->json());
    }

    public function sendMessage(Request $request)
    {
        $this->validate($request, [
            'contact_country_id' => ['required', 'exists:countries,id,status,1'],
            'contact_number' => ['required', 'string'],
            'content' => ['nullable', 'string'],
            'files.*' => ['nullable', 'file', 'image'],
        ]);

        try {
            \DB::beginTransaction();

            $country = Country::Active()->find($request->input('contact_country_id'));
            $to = PhoneService::formatE164($request->input('contact_number'), $country);
            $from = config('env.WHATSAPP_SESSION_ID');

            $payload = $this->whatsAppService->buildMessagePayload($request);
            $payload['countryCode'] = $country->iso2;

            // Log messages BEFORE sending
            $logIds = [];
            foreach ($payload['messages'] as $messageItem) {
                $log = \App\Models\BotMessage::create([
                    'channel' => 'whatsapp',
                    'direction' => 'outgoing',
                    'from_id' => $from,
                    'to_id' => $to,
                    'to_name' => null,
                    'from_name' => null,
                    'type' => $messageItem['type'],
                    'message' => $messageItem,
                    'status' => 'PENDING',
                    'reference_number' => $messageItem['reference_number'] ?? null,
                    'ip_address' => $request->ip(),
                    'sent_at' => now(), // optimistic write, can update later via webhook
                ]);
                $logIds[] = $log->id;
            }

            $response = $this->whatsAppService->sendMessage(
                config('env.WHATSAPP_SESSION_ID'),
                $to,
                $payload
            );

            // Optionally update status to SENT if no exception
            BotMessage::whereIn('id', $logIds)->update(['status' => 'SENT']);

            if (! $response->ok()) {
                throw new \Exception($response->json('message') ?? __('Unknown error'));
            }

            \DB::commit();

            return makeResponse($response->status(), null, $response->json());
        } catch (\Exception $e) {
            \DB::rollBack();

            // OPTIONAL: Cleanup uploaded media from payload
            try {
                if (! empty($payload['messages'])) {
                    foreach ($payload['messages'] as $messageItem) {
                        if (
                            $messageItem['type'] === 'image' &&
                            isset($messageItem['image']['url']) &&
                            getUploadStorage()->url('whatsapp/')
                        ) {
                            $fileUrl = $messageItem['image']['url'];
                            $filePath = \Str::after($fileUrl, rtrim(getUploadStorage()->url(''), '/').'/');
                            getUploadStorage()->delete($filePath);
                        }
                    }
                }
            } catch (\Throwable $cleanupError) {
                \Log::warning('Failed to clean up uploaded image from S3', [
                    'error' => $cleanupError->getMessage(),
                ]);
            }

            // Can i loop the payload here to delete message from S3?
            return makeResponse(false, $e);
        }
    }

    public function checkStatus(Request $request)
    {
        $response = $this->whatsAppService->getSessionStatus(config('env.WHATSAPP_SESSION_ID'));

        $data = $response->json();
        $status = $data['sessionStatus'] ?? 'disconnected'; // Use sessionStatus from microservice
        $message = $data['message'] ?? 'Unknown status.';
        $qrCode = $data['qrCode'] ?? null; // Extract qrCode from microservice response

        // The makeResponse helper expects the main status code as the first argument.
        // The 'status' key within the data array is for the Vue.js frontend.
        return makeResponse($response->status(), null, [
            'status' => 'success',
            'sessionStatus' => $status,
            'qr' => $qrCode,
            'message' => $message,
        ]);
    }
}
