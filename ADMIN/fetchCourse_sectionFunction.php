<?php
include_once("newincludes/newconfig.php");

$lesson_id = $_GET['lesson_id'] ?? '';

if (empty($lesson_id)) {
    echo json_encode(['error' => '❌ Missing lesson ID']);
    exit;
}

$sql = "SELECT lesson_id, lesson_name, description, video_id FROM lessons WHERE lesson_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();

$lessons = [];

$apiSecretKey = 'JXqFQcxdg1n3pPzKhxOQqrX5TmTUkSYynDKJjxg8lze2Az0fplLWZJJEtWjD7hX1';  // Your VdoCipher API Secret Key

while ($row = $result->fetch_assoc()) {
    $video_id = $row['video_id'];

    // ✅ Generate OTP and Playback Info dynamically
    $ch = curl_init("https://dev.vdocipher.com/api/videos/$video_id/otp");
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        "Authorization: Apisecret $apiSecretKey",
        "Content-Type: application/json"
    ]);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(['ttl' => 300]));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);

    $response = curl_exec($ch);
    $data = json_decode($response, true);

    // ✅ Handle VdoCipher API Errors
    if (isset($data['otp']) && isset($data['playbackInfo'])) {
        $otp = $data['otp'];
        $playbackInfo = $data['playbackInfo'];
    } else {
        $otp = null;
        $playbackInfo = null;
    }

    $lessons[] = [
        'lesson_id'     => $row['lesson_id'],
        'lesson_name'   => $row['lesson_name'],
        'description'   => $row['description'],
        'video_id'      => $video_id,
        'otp'           => $otp,
        'playbackInfo'  => $playbackInfo
    ];
}

echo json_encode($lessons);
?>
