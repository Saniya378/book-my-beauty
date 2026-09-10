<?php
session_start();
header('Content-Type: application/json');

include 'config.php';

$message = isset($_POST['message']) ? trim($_POST['message']) : '';
$image_data = isset($_POST['image']) ? $_POST['image'] : '';
$image_mime = isset($_POST['image_mime']) ? $_POST['image_mime'] : 'image/jpeg';
$history_raw = isset($_POST['history']) ? $_POST['history'] : '[]';

if (empty($message) && empty($image_data)) {
    echo json_encode(['reply' => 'Please type a message or attach an image.']);
    exit();
}

// ---- Pull live portal data so the AI answers with real information ----
$services_list = [];
$sq = mysqli_query($conn, "SELECT DISTINCT service_name, price FROM services WHERE is_active=1 LIMIT 40");
if ($sq) {
    while ($s = mysqli_fetch_assoc($sq)) {
        $services_list[] = $s['service_name'] . ' (Rs. ' . $s['price'] . ')';
    }
}

$beauticians_list = [];
$bq = mysqli_query($conn, "SELECT u.name, bp.location FROM beautician_profiles bp JOIN users u ON bp.user_id = u.id LIMIT 40");
if ($bq) {
    while ($b = mysqli_fetch_assoc($bq)) {
        $beauticians_list[] = $b['name'] . ' (' . ($b['location'] ?: 'location not set') . ')';
    }
}

$services_text = !empty($services_list) ? implode(', ', $services_list) : 'No services listed yet.';
$beauticians_text = !empty($beauticians_list) ? implode(', ', $beauticians_list) : 'No beauticians listed yet.';

// ---- System instructions for the AI ----
$system_prompt = "You are Bella, the AI assistant for \"Beauty Portal\", a beauty salon booking website (a university final year project). "
    . "You help visitors with: available services and prices, finding beauticians, how to book an appointment, and how reviews work. "
    . "Reply in the same language and script the user writes in (English, Urdu script, or Roman Urdu) — match their style. "
    . "Keep answers short and friendly (2-5 sentences, use line breaks for lists). "
    . "If the user asks something unrelated to the beauty portal (general chit-chat, politics, coding help, etc.), politely say you can only help with Beauty Portal questions and steer them back. "
    . "If the user sends an image, describe what's relevant to a beauty/salon context (e.g. hairstyle, skin concern, nail art) and relate it to services on the platform if possible. "
    . "\n\nCurrent services on the platform: " . $services_text
    . "\n\nCurrent beauticians on the platform: " . $beauticians_text
    . "\n\nHow booking works: Browse beauticians on the Browse page, click 'View Profile', then 'Book Now', pick a service, day, date and time, then confirm. "
    . "Bookings can be cancelled within 2 hours of booking. Reviews can be left from a beautician's profile page after browsing.";

// ---- Build conversation contents for Gemini ----
$history = json_decode($history_raw, true);
if (!is_array($history)) $history = [];

$contents = [];
foreach ($history as $turn) {
    if (!isset($turn['role']) || !isset($turn['text'])) continue;
    $role = $turn['role'] === 'user' ? 'user' : 'model';
    $contents[] = [
        'role' => $role,
        'parts' => [['text' => $turn['text']]]
    ];
}

// Current user turn (text + optional image)
$current_parts = [];
if (!empty($message)) {
    $current_parts[] = ['text' => $message];
}
if (!empty($image_data)) {
    $current_parts[] = [
        'inlineData' => [
            'mimeType' => $image_mime,
            'data' => $image_data
        ]
    ];
    if (empty($message)) {
        $current_parts[] = ['text' => 'What do you see in this image? Relate it to our beauty services if relevant.'];
    }
}

$contents[] = [
    'role' => 'user',
    'parts' => $current_parts
];

$payload = [
    'contents' => $contents,
    'systemInstruction' => [
        'parts' => [['text' => $system_prompt]]
    ],
    'generationConfig' => [
        'temperature' => 0.6,
        'maxOutputTokens' => 500
    ]
];

// ---- Call Gemini API ----
$api_key = defined('GEMINI_API_KEY') ? GEMINI_API_KEY : '';

function call_gemini($url_model, $api_key, $payload) {
    $url = "https://generativelanguage.googleapis.com/v1beta/models/" . $url_model . ":generateContent?key=" . $api_key;
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, ['Content-Type: application/json']);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true);
    curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $curl_error = curl_error($ch);
    curl_close($ch);
    return ['response' => $response, 'http_code' => $http_code, 'curl_error' => $curl_error];
}

if (empty($api_key) || $api_key === 'PASTE_YOUR_GEMINI_API_KEY_HERE') {
    echo json_encode(['reply' => "The AI assistant isn't configured yet. Please add a Gemini API key in config.php."]);
    exit();
}

// Try the primary model first, then fall back to a secondary model
// if it has been deprecated or is unavailable (Google retires model
// names fairly often — this keeps the chatbot working without edits).
$models_to_try = ['gemini-2.5-flash-lite', 'gemini-3-flash-preview'];
$result = null;

foreach ($models_to_try as $model_name) {
    $model_payload = $payload;
    if (strpos($model_name, 'gemini-3') === 0) {
        // Gemini 3 series: use thinkingLevel (minimal is the lowest available for Flash)
        $model_payload['generationConfig']['thinkingConfig'] = ['thinkingLevel' => 'low'];
    } else {
        // Gemini 2.5 series: use thinkingBudget (0 disables thinking entirely)
        $model_payload['generationConfig']['thinkingConfig'] = ['thinkingBudget' => 0];
    }

    $result = call_gemini($model_name, $api_key, $model_payload);
    if ($result['curl_error']) continue;
    $decoded = json_decode($result['response'], true);
    if ($result['http_code'] === 200 && isset($decoded['candidates'][0]['content']['parts'][0]['text'])) {
        break;
    }
}

if ($result['curl_error']) {
    echo json_encode(['reply' => "Connection error (for debugging): " . $result['curl_error']]);
    exit();
}

$http_code = $result['http_code'];
$response = $result['response'];
$result_data = json_decode($response, true);

if ($http_code !== 200 || !isset($result_data['candidates'][0]['content']['parts'][0]['text'])) {
    $error_msg = isset($result_data['error']['message']) ? $result_data['error']['message'] : 'Unknown error';
    echo json_encode(['reply' => "Sorry, I couldn't process that right now. (" . $error_msg . ")"]);
    exit();
}

$reply_text = $result_data['candidates'][0]['content']['parts'][0]['text'];

echo json_encode(['reply' => $reply_text]);
?>