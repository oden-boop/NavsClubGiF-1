<?php
include 'includes/config.php';
header('Content-Type: application/json');

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Required fields
$requiredFields = ['course_id', 'course_price'];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required.']);
        exit;
    }
}

// Assign variables & sanitize inputs
$course_id = intval($_POST['course_id']);
$course_price = filter_var($_POST['course_price'], FILTER_VALIDATE_FLOAT);
$created_at = date('Y-m-d H:i:s'); // Capture current timestamp
$status = 2; // Default status set to 2

// Validate numerical values
if ($course_price === false || $course_price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid course price value.']);
    exit;
}

// Prepare SQL query to insert into `course_cart`
$query = "INSERT INTO course_cart (course_id, course_price, created_at, status) 
          VALUES (?, ?, ?, ?)";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("idsi", $course_id, $course_price, $created_at, $status);
    
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'message' => 'Course added to cart successfully.']);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error: Unable to add course.', 'error' => $stmt->error]);
    }

    $stmt->close();
} else {
    echo json_encode(['success' => false, 'message' => 'Database error: Unable to prepare statement.', 'error' => $conn->error]);
}

$conn->close();
?>
