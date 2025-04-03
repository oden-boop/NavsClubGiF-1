<?php
session_start();
include 'includes/config.php';
header('Content-Type: application/json');

// ✅ Check if the user is logged in
if (!isset($_SESSION['usersid'])) {
    echo json_encode(['success' => false, 'message' => 'Session expired! Please log in again.']);
    exit;
}

$usersid = intval($_SESSION['usersid']);

// ✅ Debugging: Check if any POST data is received
if (empty($_POST)) {
    echo json_encode([
        'success' => false, 
        'message' => 'No POST data received. Ensure AJAX is sending data correctly.',
        'debug' => $_POST
    ]);
    exit;
}

// ✅ Validate received data
if (!isset($_POST['course_id']) || !isset($_POST['course_price'])) {
    echo json_encode([
        'success' => false, 
        'message' => 'Missing course data.', 
        'post_data' => $_POST
    ]);
    exit;
}

// ✅ Sanitize input values
$course_id = is_numeric($_POST['course_id']) ? intval($_POST['course_id']) : 0;
$course_price = is_numeric($_POST['course_price']) ? floatval($_POST['course_price']) : 0.0;

// ✅ Ensure values are valid
if ($course_id <= 0 || $course_price <= 0) {
    echo json_encode([
        'success' => false, 
        'message' => 'Invalid course data.',
        'debug' => ['course_id' => $course_id, 'course_price' => $course_price, 'raw_post' => $_POST]
    ]);
    exit;
}

// ✅ Prepare SQL statement to prevent SQL injection
$query = "INSERT INTO course_cart (usersid, course_id, course_price, created_at, status) 
          VALUES (?, ?, ?, NOW(), 2)";

$stmt = $conn->prepare($query);
$stmt->bind_param("iid", $usersid, $course_id, $course_price);

if ($stmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Course added to cart successfully.']);
} else {
    echo json_encode([
        'success' => false, 
        'message' => 'Database error. Could not add course to cart.', 
        'error' => $stmt->error
    ]);
}

$stmt->close();
$conn->close();
?>
