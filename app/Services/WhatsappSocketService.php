<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class WhatsappSocketService
{
    protected $baseUrl;
    protected $sessionId = null;

    public function __construct($session_id = null)
    {
        $this->baseUrl = config('env.WHATSAPP_API_URL');
        $this->sessionId = $session_id ?? mb_strtolower(config('app.name'));
    }

    protected function getHttpClient()
    {
        return Http::withHeaders([
            'x-api-key' => config('env.WHATSAPP_API_KEY'),
            'Accept' => 'application/json',
        ])->timeout(60);
    }

    public function getSessionId()
    {
        return $this->sessionId;
    }

    public function sendMessage($to, $content = null, $files = null, $poll = null, $poolMultipleAnswers = false)
    {
        try {
            $to = preg_replace('/\+/', '', $to);

            // --- Handle poll message (text-only payload) ---
            if ($poll && isset($poll['name']) && isset($poll['options']) && is_array($poll['options'])) {
                $response = $this->getHttpClient()
                    ->post("{$this->baseUrl}/send", [
                        'sessionId'   => $this->sessionId,
                        'to'          => $to . '@s.whatsapp.net',
                        'type'        => 'pollMessage',
                        'pollName'    => $poll['name'],
                        'pollOptions' => $poll['options'],
                        'multipleAnswers' => $poolMultipleAnswers,
                    ]);

                // --- Handle media files (multipart) ---
            } elseif ($files && is_array($files)) {
                $multipart = [
                    ['name' => 'sessionId', 'contents' => $this->sessionId],
                    ['name' => 'to',        'contents' => $to . '@s.whatsapp.net'],
                    ['name' => 'text',      'contents' => $content ?? ''],
                ];

                foreach ($files as $file) {
                    $multipart[] = [
                        'name'     => 'files[]',
                        'contents' => file_get_contents($file->getRealPath()),
                        'filename' => $file->getClientOriginalName(),
                        'headers'  => ['Content-Type' => $file->getMimeType()],
                    ];
                }

                $response = $this->getHttpClient()
                    ->asMultipart()
                    ->post("{$this->baseUrl}/send", $multipart);

                // --- Handle plain text ---
            } else {
                $response = $this->getHttpClient()
                    ->post("{$this->baseUrl}/send", [
                        'sessionId' => $this->sessionId,
                        'to'        => $to . '@s.whatsapp.net',
                        'text'      => $content,
                    ]);
            }

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);

            if (isset($json['success']) && $json['success']) {
                return ['api_success' => true];
            }

            return ['api_success' => false, 'api_message' => $json['error'] ?? 'Failed to send message', 'api_error_code' => $json['error_code'] ?? null];
        } catch (\Exception $e) {
            return ['api_success' => false, 'api_message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function checkSession()
    {
        try {
            $response = $this->getHttpClient()
                ->get("{$this->baseUrl}/session?sessionId={$this->sessionId}");

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);

            return $json['active'] ?? false;
        } catch (\Exception $e) {
            return ['api_success' => false, 'api_message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function checkOrStartSession()
    {
        try {
            $status = $this->checkSession();
            if (is_array($status) && isset($status['api_success']) && !$status['api_success']) {
                return $status; // error from checkSession
            }

            if (!$status) {
                return $this->startSession();
            }

            return [
                'api_success' => true,
                'is_active' => true,
                'qr' => null,
            ];
        } catch (\Exception $e) {
            return ['api_success' => false, 'api_message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function startSession()
    {
        try {
            $response = $this->getHttpClient()
                ->post("{$this->baseUrl}/session", [
                    'sessionId' => $this->sessionId,
                ]);

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);
            $status = strtolower($json['status'] ?? '');

            if ($status === 'pending') {
                return [
                    'api_success' => true,
                    'is_active' => false,
                    'qr' => $json['qr'] ?? null,
                ];
            }

            return [
                'api_success' => true,
                'is_active' => true,
                'qr' => null,
            ];
        } catch (\Exception $e) {
            return ['api_success' => false, 'is_active' => false, 'api_message' => 'Error: ' . $e->getMessage()];
        }
    }

    public function disconnect()
    {
        try {
            $response = $this->getHttpClient()
                ->delete("{$this->baseUrl}/session", [
                    'sessionId' => $this->sessionId,
                ]);

            $body = $response->getBody()->getContents();
            $json = json_decode($body, true);

            if (isset($json['success']) && $json['success']) {
                return ['api_success' => true, 'is_active' => false];
            }

            return ['api_success' => false, 'api_message' => 'Failed to disconnect session'];
        } catch (\Exception $e) {
            return ['api_success' => false, 'api_message' => 'Error: ' . $e->getMessage()];
        }
    }
}
