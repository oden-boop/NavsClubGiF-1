<?php
include 'includes/config.php';
header('Content-Type: application/json');

// Validate POST request
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

// Required fields
$requiredFields = ['course_name', 'course_by', 'price', 'course_level', 'course_duration'];

foreach ($requiredFields as $field) {
    if (!isset($_POST[$field]) || empty(trim($_POST[$field]))) {
        echo json_encode(['success' => false, 'message' => ucfirst($field) . ' is required.']);
        exit;
    }
}

// Assign variables & sanitize inputs
$course_name = trim($_POST['course_name']);
$course_by = trim($_POST['course_by']);
$price = filter_var($_POST['price'], FILTER_VALIDATE_FLOAT);
$course_level = trim($_POST['course_level']);
$course_duration = trim($_POST['course_duration']);

// Validate numerical values
if ($price === false || $price <= 0) {
    echo json_encode(['success' => false, 'message' => 'Invalid price value.']);
    exit;
}

// Prepare SQL query
$query = "INSERT INTO cart (course_name, course_by, price, course_level, course_duration) 
          VALUES (?, ?, ?, ?, ?)";

if ($stmt = $conn->prepare($query)) {
    $stmt->bind_param("ssdss", $course_name, $course_by, $price, $course_level, $course_duration);
    
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
