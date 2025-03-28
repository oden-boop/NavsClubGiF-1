<?php
session_start();
include 'includes/config.php';
header('Content-Type: application/json');

// ✅ Check session
if (!isset($_SESSION['usersid'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired! Please log in again.']);
    exit;
}

$usersid = intval($_SESSION['usersid']);

// ✅ Validate POST data
$course_id = isset($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$course_price = isset($_POST['course_price']) ? floatval($_POST['course_price']) : 0.0;

if ($course_id <= 0 || $course_price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid course data.']);
    exit;
}

// ✅ Insert data directly
$query = "INSERT INTO course_cart (usersid, course_id, course_price, created_at, status) 
          VALUES (?, ?, ?, NOW(), 2)";

$stmt = $conn->prepare($query);
$stmt->bind_param("iid", $usersid, $course_id, $course_price);

$response = $stmt->execute()
    ? ['success' => true, 'message' => 'Course added to cart successfully.']
    : ['success' => false, 'message' => 'Database error.', 'error' => $stmt->error];

$stmt->close();
$conn->close();

echo json_encode($response);
?>
