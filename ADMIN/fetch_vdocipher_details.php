<?php
// ✅ Include VdoCipher Configuration
require_once 'newincludes/vdo_cipher_config.php';

// ✅ Sanitize & Validate Video ID
$video_id = $_GET['video_id'] ?? '';
$video_id = htmlspecialchars(strip_tags(trim($video_id)));

if (empty($video_id) || !preg_match('/^[a-zA-Z0-9]{32}$/', $video_id)) {
    echo json_encode(['error' => '❌ Invalid or missing video ID'], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Initialize cURL Session
$ch = curl_init();

// ✅ Function to Execute cURL and Return JSON Response
function fetchVdoCipherData($url, $method = 'GET', $data = null) {
    global $ch;

    curl_setopt_array($ch, [
        CURLOPT_URL => $url,
        CURLOPT_HTTPHEADER => [
            "Authorization: Apisecret " . VDOCIPHER_API_KEY,
            "Content-Type: application/json"
        ],
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_CUSTOMREQUEST => $method,
    ]);

    if ($method === 'POST' && $data) {
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    }

    $response = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);

    if ($response === false || $http_code !== 200) {
        return ['error' => "❌ HTTP $http_code - " . curl_error($ch), 'raw_response' => $response];
    }

    return json_decode($response, true);
}

// ✅ Fetch Video Metadata
$metadataUrl = "https://dev.vdocipher.com/api/videos/{$video_id}";
$metadata = fetchVdoCipherData($metadataUrl);

if (isset($metadata['error'])) {
    echo json_encode(['error' => $metadata['error']], JSON_PRETTY_PRINT);
    exit;
}

if (empty($metadata) || !isset($metadata['id'])) {
    echo json_encode(['error' => '❌ Video metadata not found'], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Fetch OTP for Playback (Debugging added)
$otpUrl = "https://dev.vdocipher.com/api/videos/{$video_id}/otp";
$otpData = fetchVdoCipherData($otpUrl, 'POST', ['ttl' => 300]);

if (!isset($otpData['otp']) || !isset($otpData['playbackInfo'])) {
    echo json_encode([
        'error' => '❌ OTP or playbackInfo missing',
        'raw_response' => $otpData // ✅ Shows raw API response for debugging
    ], JSON_PRETTY_PRINT);
    exit;
}

// ✅ Extract and Format Duration
$durationInSeconds = $metadata['duration'] ?? 0;
$durationFormatted = "Unknown duration";
if ($durationInSeconds > 0) {
    $hours = floor($durationInSeconds / 3600);
    $minutes = floor(($durationInSeconds % 3600) / 60);
    $seconds = $durationInSeconds % 60;
    $durationFormatted = sprintf("%02d:%02d:%02d", $hours, $minutes, $seconds);
}

// ✅ Combine Full Metadata + OTP
echo json_encode([
    'video_id' => $metadata['id'] ?? 'N/A',
    'title' => $metadata['title'] ?? 'No title available',
    'description' => $metadata['description'] ?? 'No description available',
    'duration' => $durationFormatted,
    'status' => $metadata['status'] ?? 'N/A',
    'poster' => $metadata['poster'] ?? 'N/A',
    'thumbnail' => $metadata['thumbnail'] ?? $metadata['poster'] ?? 'N/A',
    'otp' => $otpData['otp'],
    'playbackInfo' => $otpData['playbackInfo']
], JSON_PRETTY_PRINT);

// ✅ Close cURL session
curl_close($ch);
?>
