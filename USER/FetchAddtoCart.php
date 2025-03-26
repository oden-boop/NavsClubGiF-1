<?php
session_start();
include("includes/config.php");

// Check if the user is logged in
if (!isset($_SESSION['usersid'])) {
    header("Location: NavsClubGIF/NavsClubGIF/LOGIN/LoginAccount.php");
    exit();
}

$usersid = $_SESSION['usersid'];

// 🔎 Step 1: Fetch User Email from `personal_information`
$sql_user = "SELECT email FROM personal_information WHERE usersid = ?";
$stmt_user = $conn->prepare($sql_user);
$stmt_user->bind_param("i", $usersid);
$stmt_user->execute();
$result_user = $stmt_user->get_result();

if ($result_user->num_rows == 0) {
    echo "<script>alert('❌ Error: User email not found!'); window.location.href = 'MyCart.php';</script>";
    exit();
}

$userData = $result_user->fetch_assoc();
$email = $userData['email'];
$_SESSION['email'] = $email; // Store email in session for later use

// 🔎 Step 2: Check if course_id is posted
if (!isset($_POST['course_id'])) {
    echo "<script>alert('❌ Error: Course ID missing!'); window.location.href = 'MyCart.php';</script>";
    exit();
}

$course_id = intval($_POST['course_id']);
$cart_id = intval($_POST['cart_id']);

// 🔎 Step 3: Fetch Course Details
$sql_course = "SELECT course_name, course_price, course_instructor FROM courses WHERE course_id = ?";
$stmt_course = $conn->prepare($sql_course);
$stmt_course->bind_param("i", $course_id);
$stmt_course->execute();
$result_course = $stmt_course->get_result();

if ($result_course->num_rows == 0) {
    echo "<script>alert('❌ Error: Course details not found!'); window.location.href = 'MyCart.php';</script>";
    exit();
}

$course = $result_course->fetch_assoc();
$course_name = $course['course_name'];
$course_price = $course['course_price'];
$course_instructor = $course['course_instructor'];

// 🔹 Step 4: Auto-generate `order_id`
$order_id = uniqid('ORD_');
$_SESSION['order_id'] = $order_id; // Store order ID in session

// 🔥 Step 5: Insert data into `checkout_course`
$insert_checkout = "
    INSERT INTO checkout_course (
        usersid, order_id, course_id, course_name, 
        course_price, course_instructor, status
    ) 
    VALUES (?, ?, ?, ?, ?, ?, 2)";
    
$stmt_checkout = $conn->prepare($insert_checkout);

if (!$stmt_checkout) {
    die("SQL Error: " . $conn->error); // Debugging in case of query issue
}

$stmt_checkout->bind_param("isisss", 
    $usersid, $order_id, $course_id, 
    $course_name, $course_price, $course_instructor
);

if ($stmt_checkout->execute()) {
    // ✅ Step 6: Update `course_cart` status to indicate purchase
    $update_cart = "UPDATE course_cart SET status = 2 WHERE cart_id = ?";
    $stmt_update = $conn->prepare($update_cart);
    $stmt_update->bind_param("i", $cart_id);
    $stmt_update->execute();

    echo "<script>
        alert('✅ Proceeding to Payment...');
        setTimeout(function() {
            window.location.href = 'CourseForCheckout.php';
        }, 2000);
    </script>";
} else {
    echo "<script>alert('❌ Error during checkout. Please try again.');</script>";
}
?>
