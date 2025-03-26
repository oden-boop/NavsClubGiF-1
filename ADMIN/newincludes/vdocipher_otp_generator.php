<?php
// ✅ Include Configuration
require_once 'vdo_cipher_config.php';  

// ✅ Fetch & Sanitize Video ID
$videoId = $_GET['video_id'] ?? '';              // 🔥 Get from URL parameter
$videoId = htmlspecialchars(strip_tags($videoId)); // ✅ Sanitize input

// ✅ Check if the video ID is empty
if (empty($videoId)) {
    echo json_encode(['error' => '❌ Missing or invalid video ID'], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Request URL
$url = VDOCIPHER_BASE_URL . "/videos/{$videoId}/otp";

// ✅ Request Payload
$data = json_encode([
    'ttl' => VDOCIPHER_TOKEN_EXPIRY
]);

// ✅ Initialize cURL
$ch = curl_init($url);

curl_setopt_array($ch, [
    CURLOPT_RETURNTRANSFER => true,
    CURLOPT_POST => true,
    CURLOPT_HTTPHEADER => [
        'Authorization: Apisecret ' . VDOCIPHER_API_KEY,
        'Content-Type: application/json'
    ],
    CURLOPT_POSTFIELDS => $data,
    CURLOPT_TIMEOUT => 10,                  // 🔥 Timeout in 10s
    CURLOPT_FAILONERROR => true             // ✅ Trigger cURL error on HTTP error
]);

// ✅ Execute cURL and Handle the Response
$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

// ✅ Handle cURL Errors
if ($response === false) {
    echo json_encode(['error' => "❌ cURL Error: $curlError"], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Handle HTTP Errors
if ($httpCode !== 200) {
    echo json_encode([
        'error' => "❌ API Error: HTTP $httpCode",
        'response' => json_decode($response, true) ?? $response
    ], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Parse and Display the API Response
$data = json_decode($response, true);

if (isset($data['otp'], $data['playbackInfo'])) {
    echo json_encode([
        'success' => true,
        'otp' => $data['otp'],
        'playbackInfo' => $data['playbackInfo']
    ], JSON_PRETTY_PRINT);
} else {
    echo json_encode(['error' => '❌ Invalid API response'], JSON_PRETTY_PRINT);
}
?>
