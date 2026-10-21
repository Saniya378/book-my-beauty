<?php

namespace App\Services;

use App\Models\BeauticianProfile;
use App\Models\Service;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class GeminiChatService
{
    protected string $apiKey;

    public function __construct()
    {
        $this->apiKey = env('GEMINI_API_KEY', '');
    }

    public function chat(string $message = '', ?string $imageData = null, string $imageMime = 'image/jpeg', array $history = [], ?string $audioData = null, string $audioMime = 'audio/webm'): string
    {
        if (empty($this->apiKey) || $this->apiKey === 'PASTE_YOUR_GEMINI_API_KEY_HERE') {
            return "The AI assistant isn't configured yet. Please ensure GEMINI_API_KEY is configured in .env.";
        }

        // Cache portal context for 5 minutes to eliminate DB overhead on every message
        $context = \Illuminate\Support\Facades\Cache::remember('bella_portal_context', 300, function () {
            $services = Service::where('is_active', 1)->limit(30)->get(['service_name', 'price']);
            $servicesText = $services->isNotEmpty()
                ? $services->map(fn($s) => "{$s->service_name} (Rs. {$s->price})")->join(', ')
                : 'No services listed yet.';

            $beauticians = BeauticianProfile::with('user')->limit(30)->get();
            $beauticiansText = $beauticians->isNotEmpty()
                ? $beauticians->map(fn($b) => ($b->user->name ?? 'Beautician') . ' (' . ($b->location ?: 'location not set') . ')')->join(', ')
                : 'No beauticians listed yet.';

            return [
                'services' => $servicesText,
                'beauticians' => $beauticiansText,
            ];
        });

        $systemPrompt = "You are Bella, the fast and friendly AI assistant for \"BookMyBeauty\" salon platform. "
            . "Always reply in the same language as the user (English, Urdu, or Roman Urdu). Never use Hindi words or Devanagari script. "
            . "Keep your answers very fast, concise, polite, and direct (1 to 2 short sentences only). "
            . "Services available: {$context['services']}. "
            . "Beauticians: {$context['beauticians']}. "
            . "Booking guide: Go to Browse page -> Select Beautician -> Choose service & slot -> Confirm.";

        $contents = [];

        // Keep last 3 turns only for faster context processing
        $recentHistory = array_slice($history, -6);
        foreach ($recentHistory as $turn) {
            if (!isset($turn['role']) || !isset($turn['text'])) continue;
            $contents[] = [
                'role' => $turn['role'] === 'user' ? 'user' : 'model',
                'parts' => [['text' => $turn['text']]],
            ];
        }

        $currentParts = [];
        if (!empty($message)) {
            $currentParts[] = ['text' => $message];
        }

        if (!empty($imageData)) {
            $currentParts[] = [
                'inlineData' => [
                    'mimeType' => $imageMime,
                    'data' => $imageData,
                ],
            ];
            if (empty($message) && empty($audioData)) {
                $currentParts[] = ['text' => 'Describe this beauty style briefly and suggest related services.'];
            }
        }

        if (!empty($audioData)) {
            $currentParts[] = [
                'inlineData' => [
                    'mimeType' => $audioMime,
                    'data' => $audioData,
                ],
            ];
            if (empty($message)) {
                $currentParts[] = ['text' => 'Listen to the user\'s spoken question in this audio and reply to them accurately in Urdu or English as Bella.'];
            }
        }

        $contents[] = [
            'role' => 'user',
            'parts' => $currentParts,
        ];

        $payload = [
            'contents' => $contents,
            'systemInstruction' => [
                'parts' => [['text' => $systemPrompt]],
            ],
            'generationConfig' => [
                'temperature' => 0.4,
                'maxOutputTokens' => 250,
            ],
        ];

        // Active fast models on Google Generative Language API
        $modelsToTry = [
            'gemini-flash-lite-latest',
            'gemini-flash-latest',
            'gemini-3.5-flash-lite',
            'gemini-3.7-flash',
        ];

        foreach ($modelsToTry as $model) {
            try {
                $response = Http::timeout(8)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", $payload);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                        return trim($json['candidates'][0]['content']['parts'][0]['text']);
                    }
                } else {
                    Log::warning("Gemini model {$model} returned status " . $response->status() . ": " . substr($response->body(), 0, 150));
                }
            } catch (\Exception $e) {
                Log::warning("Gemini model {$model} failed: " . $e->getMessage());
                continue;
            }
        }

        return "I'm having a little trouble connecting right now. Feel free to browse our beauticians or try again!";
    }

    public function transcribe(string $audioData, string $audioMime = 'audio/webm'): string
    {
        if (empty($this->apiKey)) {
            return '';
        }

        $payload = [
            'contents' => [
                [
                    'role' => 'user',
                    'parts' => [
                        [
                            'inlineData' => [
                                'mimeType' => $audioMime,
                                'data' => $audioData,
                            ],
                        ],
                        [
                            'text' => 'Transcribe the spoken audio verbatim into clean text. If the language is Urdu or Pakistani dialect, transcribe in Roman Urdu or Urdu script (or English if spoken in English). Do NOT transcribe into Hindi or Devanagari script. Output ONLY the transcribed words with no other text.',
                        ],
                    ],
                ],
            ],
            'generationConfig' => [
                'temperature' => 0.1,
                'maxOutputTokens' => 150,
            ],
        ];

        $models = ['gemini-flash-lite-latest', 'gemini-3.5-transcribe', 'gemini-flash-latest'];

        foreach ($models as $m) {
            try {
                $res = Http::timeout(8)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$m}:generateContent?key={$this->apiKey}", $payload);

                if ($res->successful()) {
                    $json = $res->json();
                    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                        return trim($json['candidates'][0]['content']['parts'][0]['text']);
                    }
                }
            } catch (\Exception $e) {
                continue;
            }
        }

        return '';
    }
}

