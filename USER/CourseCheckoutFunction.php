<?php
session_start();
require_once 'includes/config.php'; // ✅ Secure DB connection

// ✅ Validate User Session
if (!isset($_SESSION['usersid'])) {
    die("❌ Session expired! Please log in again.");
}

// ✅ Sanitize Session Data
$usersid = intval($_SESSION['usersid']);
$fullname = $_SESSION['fullname'] ?? 'Guest';
$email = $_SESSION['email'] ?? '';

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    die("❌ Invalid email format.");
}

// ✅ Validate Payment Details
$cardNumber = $_POST['cardNumber'] ?? '';
$expiryDate = $_POST['expiryDate'] ?? '';
$cvv = $_POST['cvv'] ?? '';

if (empty($cardNumber) || empty($expiryDate) || empty($cvv)) {
    die("❌ Please complete all payment fields.");
}

// ✅ Start Transaction for Atomicity
$conn->begin_transaction();

try {
    // 🔍 Fetch ONLY ONE `checkout_course` with Status = 2 (LIMIT 1)
    $stmt_fetch = $conn->prepare("
        SELECT course_id, order_id, course_name, course_instructor, course_price 
        FROM checkout_course 
        WHERE usersid = ? AND status = 2
        LIMIT 1
    ");
    
    if (!$stmt_fetch) {
        throw new Exception("Fetch Prepare Failed: " . $conn->error);
    }
    
    $stmt_fetch->bind_param("i", $usersid);
    $stmt_fetch->execute();
    $result = $stmt_fetch->get_result();

    if ($result->num_rows === 0) {
        throw new Exception("❌ No courses found with status 2.");
    }

    // ✅ Fetch the first course
    $row = $result->fetch_assoc();
    $course_id = $row['course_id'];
    $order_id = $row['order_id'];
    $course_name = $row['course_name'];
    $course_instructor = $row['course_instructor'];
    $course_price = $row['course_price'];

    if (empty($course_id)) {
        throw new Exception("❌ Missing Course ID in checkout_course.");
    }

    // ✅ Insert into `payments`
    $stmt_payment = $conn->prepare("
        INSERT INTO payments (
            usersid, course_id, order_id, full_name, email, card_number, expiry_date, amount, payment_status, transaction_date, course_name
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, 3, NOW(), ?)
    ");
    
    if (!$stmt_payment) {
        throw new Exception("Payment Prepare Failed: " . $conn->error);
    }

    $stmt_payment->bind_param(
        "iissssdds",
        $usersid, $course_id, $order_id, $fullname, $email, $cardNumber, $expiryDate, $course_price, $course_name
    );

    if (!$stmt_payment->execute()) {
        throw new Exception("Payment Insert Error: " . $stmt_payment->error);
    }

    // ✅ Insert into `my_courses`
    $stmt_my_courses = $conn->prepare("
        INSERT INTO my_courses (usersid, course_id, course_name, course_instructor, status) 
        VALUES (?, ?, ?, ?, 3)
    ");
    
    if (!$stmt_my_courses) {
        throw new Exception("My Courses Prepare Failed: " . $conn->error);
    }

    $stmt_my_courses->bind_param("iiss", $usersid, $course_id, $course_name, $course_instructor);

    if (!$stmt_my_courses->execute()) {
        throw new Exception("My Courses Insert Error: " . $stmt_my_courses->error);
    }

    // ✅ Delete from `course_cart`
    $stmt_delete_cart = $conn->prepare("
        DELETE FROM course_cart WHERE usersid = ? AND course_id = ?
    ");
    if (!$stmt_delete_cart) {
        throw new Exception("Delete Cart Prepare Failed: " . $conn->error);
    }

    $stmt_delete_cart->bind_param("ii", $usersid, $course_id);

    if (!$stmt_delete_cart->execute()) {
        throw new Exception("Delete Cart Error: " . $stmt_delete_cart->error);
    }

    // ✅ Delete from `checkout_course`
    $stmt_delete_checkout = $conn->prepare("
        DELETE FROM checkout_course WHERE usersid = ? AND course_id = ?
    ");
    if (!$stmt_delete_checkout) {
        throw new Exception("Delete Checkout Prepare Failed: " . $conn->error);
    }

    $stmt_delete_checkout->bind_param("ii", $usersid, $course_id);

    if (!$stmt_delete_checkout->execute()) {
        throw new Exception("Delete Checkout Error: " . $stmt_delete_checkout->error);
    }

    // ✅ Commit Transaction
    $conn->commit();
    echo "✅ Payment Successful! Course added to My Courses.";

} catch (Exception $e) {
    // ❌ Rollback on Error
    $conn->rollback();
    echo "❌ Error: " . $e->getMessage();
}

// ✅ Close Statements & Connection
$stmt_fetch->close();
$stmt_payment->close();
$stmt_my_courses->close();
$stmt_delete_cart->close();
$stmt_delete_checkout->close();
$conn->close();
?>
