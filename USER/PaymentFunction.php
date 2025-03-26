<?php
session_start();
$conn = new mysqli("localhost", "root", "", "navsclubs");

// ✅ Check for connection errors
if ($conn->connect_error) {
    die(json_encode(["error" => "❌ Database Connection Failed: " . $conn->connect_error]));
}

// ✅ Ensure user is logged in
if (!isset($_SESSION["usersid"])) {
    die(json_encode(["error" => "❌ Error: User is not logged in."]));
}

$usersid = $_SESSION["usersid"];

// ✅ Fetch course_id from session
if (!isset($_SESSION["course_id"])) {
    die(json_encode(["error" => "❌ Error: Course ID is missing."]));
}
$course_id = $_SESSION["course_id"];

// ✅ Validate POST data
if (!isset($_POST["cardNumber"], $_POST["expiryDate"], $_POST["cvv"])) {
    die(json_encode(["error" => "❌ Missing payment details."]));
}

$card_number = $_POST["cardNumber"];
$expiry_date = $_POST["expiryDate"];
$cvv = password_hash($_POST["cvv"], PASSWORD_DEFAULT); // Hash CVV for security

// ✅ Fetch user details (fullname, email)
$query = $conn->prepare("SELECT fullname, email FROM personal_information WHERE usersid = ?");
if (!$query) {
    die(json_encode(["error" => "❌ SQL Error (Fetching User Details): " . $conn->error]));
}
$query->bind_param("i", $usersid);
$query->execute();
$query->bind_result($full_name, $email);
$query->fetch();
$query->close();

// ✅ Fetch course details (price, name)
$query2 = $conn->prepare("SELECT course_price, course_name FROM courses WHERE course_id = ?");
if (!$query2) {
    die(json_encode(["error" => "❌ SQL Error (Fetching Course Details): " . $conn->error]));
}
$query2->bind_param("i", $course_id);
$query2->execute();
$query2->bind_result($course_price, $course_name);
$query2->fetch();
$query2->close();

// ✅ Start transaction for consistency
$conn->begin_transaction();

try {
    // ✅ Update course_cart status
    $sql1 = "UPDATE course_cart SET status = 'checked_out' WHERE usersid = ? AND course_id = ?";
    $stmt1 = $conn->prepare($sql1);
    if (!$stmt1) {
        throw new Exception("❌ SQL Error (Update Course Cart): " . $conn->error);
    }
    $stmt1->bind_param("ii", $usersid, $course_id);
    $stmt1->execute();
    $stmt1->close();

    // ✅ Insert payment details
    $sql2 = "INSERT INTO payments (usersid, course_id, full_name, email, amount, payment_status, transaction_date, card_number, expiry_date, cvv, course_name) 
             VALUES (?, ?, ?, ?, ?, 'completed', NOW(), ?, ?, ?, ?)";
    $stmt2 = $conn->prepare($sql2);
    if (!$stmt2) {
        throw new Exception("❌ SQL Error (Insert Payment): " . $conn->error);
    }
    $stmt2->bind_param("iissdssss", $usersid, $course_id, $full_name, $email, $course_price, $card_number, $expiry_date, $cvv, $course_name);
    $stmt2->execute();
    $stmt2->close();

    // ✅ Move course to my_courses
    $sql3 = "INSERT INTO my_courses (usersid, course_id) SELECT usersid, course_id FROM course_cart WHERE usersid = ? AND course_id = ?";
    $stmt3 = $conn->prepare($sql3);
    if (!$stmt3) {
        throw new Exception("❌ SQL Error (Insert My Courses): " . $conn->error);
    }
    $stmt3->bind_param("ii", $usersid, $course_id);
    $stmt3->execute();
    $stmt3->close();

    // ✅ Delete from course_cart
    $sql4 = "DELETE FROM course_cart WHERE usersid = ? AND course_id = ?";
    $stmt4 = $conn->prepare($sql4);
    if (!$stmt4) {
        throw new Exception("❌ SQL Error (Delete Course Cart): " . $conn->error);
    }
    $stmt4->bind_param("ii", $usersid, $course_id);
    $stmt4->execute();
    $stmt4->close();

    // ✅ Commit transaction
    $conn->commit();

    // ✅ Close connection
    $conn->close();

    // ✅ Return success response to JavaScript
    echo json_encode(["success" => true, "message" => "✅ Payment Successful"], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    // ❌ Rollback on failure
    $conn->rollback();
    die(json_encode(["error" => $e->getMessage()]));
}
?>
