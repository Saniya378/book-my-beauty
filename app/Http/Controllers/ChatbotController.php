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
        $historyRaw = $request->input('history', '[]');

        if (empty($message) && empty($imageData)) {
            return response()->json(['reply' => 'Please type a message or attach an image.']);
        }

        $history = json_decode($historyRaw, true);
        if (!is_array($history)) {
            $history = [];
        }

        $reply = $chatService->chat($message, $imageData, $imageMime, $history);

        return response()->json(['reply' => $reply]);
    }
}
