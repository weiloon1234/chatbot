<?php

namespace App\Services;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class WhatsAppService
{
    protected $apiKey;

    protected $baseUrl;

    public function __construct()
    {
        $this->apiKey = config('env.WHATSAPP_API_KEY');
        $this->baseUrl = config('env.WHATSAPP_API_URL');
    }

    public function startSession(string $sessionId)
    {
        return Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->post($this->baseUrl.'/sessions/start', [
                'sessionId' => $sessionId,
            ]);
    }

    public function terminateSession(string $sessionId)
    {
        return Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->post($this->baseUrl.'/sessions/terminate', [
                'sessionId' => $sessionId,
            ]);
    }

    public function getSessionStatus(string $sessionId)
    {
        return Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->get($this->baseUrl.'/sessions/status/'.$sessionId);
    }

    public function sendMessage(string $sessionId, string $to, array $payload)
    {
        return Http::withHeaders(['X-API-Key' => $this->apiKey])
            ->post($this->baseUrl.'/messages/send', [
                'sessionId' => $sessionId,
                'to' => $to,
                ...$payload,
            ]);
    }

    public function buildMessagePayload(Request $request): array
    {
        $messages = [];
        $text = trim($request->input('content'));
        $files = $request->file('files');

        if (! empty($text)) {
            $messages[] = [
                'type' => 'text',
                'text' => $text,
                'referenceNumber' => 'text-'.uniqid(),
                'typingDelay' => rand(1000, 4000),
            ];
        }

        if ($files && is_array($files)) {
            foreach ($files as $file) {
                $path = 'whatsapp/'.now()->format('Y/m/');
                $name = now()->format('d').'-'.uniqid().'.'.$file->getClientOriginalExtension();

                getUploadStorage()->put(
                    $path.$name,
                    file_get_contents($file->getRealPath())
                );

                $messages[] = [
                    'type' => 'image',
                    'image' => [
                        'url' => getUploadStorage()->url($path.$name),
                        'fileName' => $file->getClientOriginalName(),
                        'mimetype' => $file->getMimeType(),
                    ],
                    'referenceNumber' => 'img-'.uniqid(),
                    'typingDelay' => rand(1000, 4000),
                ];
            }
        }

        /**
         * POLL DEMO
         */
        //        $messages[] = [
        //            "type" => "poll",
        //            "referenceNumber" =>  "poll-1",
        //            "typingDelay" => rand(1000, 4000),
        //            "poll" => [
        //                "name" => "What's your favorite color?",
        //                "options" => ["Red", "Green", "Blue"],
        //                "selectableCount" => 2
        //            ]
        //        ];

        /**
         * LOCATION DEMO
         */
        //        $messages[] = [
        //            "type" => "location",
        //            "referenceNumber" =>  "location-1",
        //            "typingDelay" => rand(1000, 4000),
        //            "location" => [
        //                "latitude" => 3.139,
        //                "longitude" => 101.6869,
        //                "name" =>  "KLCC Tower",
        //                "address" => "Kuala Lumpur, Malaysia",
        //            ]
        //        ];

        /**
         * DOCUMENT DEMO
         */
        //        $filePath = public_path('robots.txt'); // Adjust file path and name accordingly
        //
        //        if (!file_exists($filePath)) {
        //            throw new \Exception('File not found: ' . $filePath);
        //        }
        //
        //        $messages[] = [
        //            'type' => 'document',
        //            'document' => [
        //                'base64' => base64_encode(file_get_contents($filePath)),
        //                'fileName' => 'robots.txt',
        //                'mimetype' => \Illuminate\Support\Facades\File::mimeType($filePath),
        //            ],
        //            'referenceNumber' => 'doc-' . uniqid(),
        //            'typingDelay' => rand(1000, 4000),
        //        ];

        if (empty($messages)) {
            throw new \Exception('Message must contain at least one text or image item.');
        }

        return [
            'messages' => $messages,
            'sendSequence' => $request->input('send_sequence') ?? ['image', 'text', 'location', 'poll', 'document', 'video', 'audio'],
        ];
    }
}
