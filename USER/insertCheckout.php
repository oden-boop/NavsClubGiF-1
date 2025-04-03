<?php
include("includes/config.php");

// Start session only if not already started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in
if (!isset($_SESSION['usersid'])) {
    exit('Unauthorized access');
}

$usersid = $_SESSION['usersid'];

// Get data from AJAX POST request & sanitize inputs
$course_id = intval($_POST['course_id'] ?? 0); // Ensure numeric value
$order_id = trim($_POST['order_id'] ?? '');

// Validate required fields
if (empty($order_id) || $course_id <= 0) {
    exit('Invalid input data');
}

// Fetch data from course_cart table
$cart_check_query = "SELECT cart_id, course_id, status FROM course_cart WHERE usersid = ? AND course_id = ? AND status = 2";
$cart_check_stmt = $conn->prepare($cart_check_query);
$cart_check_stmt->bind_param("ii", $usersid, $course_id);
$cart_check_stmt->execute();
$cart_check_stmt->store_result();

// If no matching cart entry is found, exit
if ($cart_check_stmt->num_rows === 0) {
    exit('No valid cart found for this course');
}

// Fetch the cart details
$cart_check_stmt->bind_result($cart_id, $fetched_course_id, $status);
$cart_check_stmt->fetch();
$cart_check_stmt->close();

// Log the fetched cart details (optional debugging)
error_log("Fetched Cart: Cart ID=$cart_id, Course ID=$fetched_course_id, Status=$status");

// Proceed with inserting into checkout_course table
$status = '2'; // Set status to 2
$query = "INSERT INTO checkout_course (usersid, course_name, course_price, order_id, status, created_at, course_id) 
          VALUES (?, '', '', ?, ?, NOW(), ?)";

$stmt = $conn->prepare($query);
$stmt->bind_param("isssi", $usersid, $order_id, $status, $fetched_course_id);

if ($stmt->execute()) {
    echo 'success';
} else {
    error_log("Database Error: " . $stmt->error); // Log error for debugging
    exit('Database error: ' . $stmt->error); // Send detailed error to the frontend
}

// Close connections
$stmt->close();
$conn->close();
?>
