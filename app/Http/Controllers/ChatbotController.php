<?php

namespace App\Http\Controllers;

use App\Services\GeminiChatService;
use Illuminate\Http\Request;

class ChatbotController extends Controller
{
    public function sendMessage(Request $request, GeminiChatService $chatService)
    {
        $message = trim($request->input('message', ''));
        $imageData = $request->input('image');
        $imageMime = $request->input('image_mime', 'image/jpeg');
        $audioData = $request->input('audio');
        $audioMime = $request->input('audio_mime', 'audio/webm');
        $historyRaw = $request->input('history', '[]');

        if (empty($message) && empty($imageData) && empty($audioData)) {
            return response()->json(['reply' => 'Please type a message, speak, or attach an image.']);
        }

        $history = json_decode($historyRaw, true);
        if (!is_array($history)) {
            $history = [];
        }

        $reply = $chatService->chat($message, $imageData, $imageMime, $history, $audioData, $audioMime);

        return response()->json(['reply' => $reply]);
    }

    public function transcribeAudio(Request $request, GeminiChatService $chatService)
    {
        $audioData = $request->input('audio');
        $audioMime = $request->input('audio_mime', 'audio/webm');

        if (empty($audioData)) {
            return response()->json(['text' => '']);
        }

        $text = $chatService->transcribe($audioData, $audioMime);

        return response()->json(['text' => $text]);
    }
}
