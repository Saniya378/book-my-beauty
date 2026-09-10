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

    public function chat(string $message = '', ?string $imageData = null, string $imageMime = 'image/jpeg', array $history = []): string
    {
        if (empty($this->apiKey) || $this->apiKey === 'PASTE_YOUR_GEMINI_API_KEY_HERE') {
            return "The AI assistant isn't configured yet. Please ensure GEMINI_API_KEY is configured in .env.";
        }

        // Pull live portal data so Bella has updated real-time information
        $services = Service::where('is_active', 1)->limit(40)->get(['service_name', 'price']);
        $servicesText = $services->isNotEmpty()
            ? $services->map(fn($s) => "{$s->service_name} (Rs. {$s->price})")->join(', ')
            : 'No services listed yet.';

        $beauticians = BeauticianProfile::with('user')->limit(40)->get();
        $beauticiansText = $beauticians->isNotEmpty()
            ? $beauticians->map(fn($b) => ($b->user->name ?? 'Beautician') . ' (' . ($b->location ?: 'location not set') . ')')->join(', ')
            : 'No beauticians listed yet.';

        $systemPrompt = "You are Bella, the friendly AI assistant for \"Beauty Portal\", a premier beauty salon booking platform. "
            . "You help visitors with: available services and prices, finding beauticians, how to book an appointment, and how reviews work. "
            . "Reply in the same language and script the user writes in (English, Urdu script, or Roman Urdu) — match their style. "
            . "Keep answers short and friendly (2-5 sentences, use line breaks for lists). "
            . "If the user asks something unrelated to the beauty portal (general chit-chat, politics, coding help, etc.), politely say you can only help with Beauty Portal questions and steer them back. "
            . "If the user sends an image, describe what's relevant to a beauty/salon context (e.g. hairstyle, skin concern, nail art) and relate it to services on the platform if possible. "
            . "\n\nCurrent services on the platform: {$servicesText}"
            . "\n\nCurrent beauticians on the platform: {$beauticiansText}"
            . "\n\nHow booking works: Browse beauticians on the Browse page, click 'View Profile', then 'Book Now', pick a service, day, date and time, then confirm. "
            . "Bookings can be cancelled within 2 hours of booking. Reviews can be left from a beautician's profile page.";

        $contents = [];

        foreach ($history as $turn) {
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
            if (empty($message)) {
                $currentParts[] = ['text' => 'What do you see in this image? Relate it to our beauty services if relevant.'];
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
                'temperature' => 0.6,
                'maxOutputTokens' => 600,
            ],
        ];

        // Try primary and fallback models
        $modelsToTry = ['gemini-2.5-flash-lite', 'gemini-1.5-flash', 'gemini-2.0-flash', 'gemini-3-flash-preview'];

        foreach ($modelsToTry as $model) {
            $modelPayload = $payload;
            if (str_starts_with($model, 'gemini-3')) {
                $modelPayload['generationConfig']['thinkingConfig'] = ['thinkingLevel' => 'low'];
            } elseif (str_starts_with($model, 'gemini-2.5')) {
                $modelPayload['generationConfig']['thinkingConfig'] = ['thinkingBudget' => 0];
            }

            try {
                $response = Http::timeout(25)
                    ->withHeaders(['Content-Type' => 'application/json'])
                    ->post("https://generativelanguage.googleapis.com/v1beta/models/{$model}:generateContent?key={$this->apiKey}", $modelPayload);

                if ($response->successful()) {
                    $json = $response->json();
                    if (isset($json['candidates'][0]['content']['parts'][0]['text'])) {
                        return $json['candidates'][0]['content']['parts'][0]['text'];
                    }
                }
            } catch (\Exception $e) {
                Log::warning("Gemini model {$model} failed: " . $e->getMessage());
                continue;
            }
        }

        return "I'm having a little trouble connecting to my beauty knowledge right now. Please feel free to browse our beauticians or try again in a moment!";
    }
}
