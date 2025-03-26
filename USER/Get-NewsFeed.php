<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json");

// Check if config.php exists
if (!file_exists('includes/newsconfig.php')) {
    echo json_encode(["status" => "error", "message" => "Missing config.php"]);
    exit;
}

include 'newsconfig.php';

// Get query or set default
$query = isset($_GET['q']) ? urlencode($_GET['q']) : 'maritime';
$url = "https://newsapi.org/v2/everything?q=$query&apiKey=" . NEWS_API_KEY;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    'User-Agent: MyNewsApp/1.0',
    'Accept: application/json'
]);

$response = curl_exec($ch);
$http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
curl_close($ch);

// Validate JSON
if ($http_code !== 200) {
    echo json_encode(["status" => "error", "message" => "Failed to fetch news."]);
    exit;
}

echo $response;
?>
