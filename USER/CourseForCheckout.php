<?php
session_start();
require_once 'includes/config.php'; // ✅ Secure DB connection

// ✅ Ensure the user is authenticated
if (!isset($_SESSION['authenticated'])) {
    echo "<script>alert('Session expired! Please log in again.'); window.location.href='../LOGIN/LoginAccount.php';</script>";
    exit();
}

$usersid = intval($_SESSION['usersid'] ?? 0); // ✅ Secure session handling
$fullname = $_SESSION['fullname'] ?? 'Guest';

// ✅ Fetch email from `personal_information`
$sql_email = "SELECT email FROM personal_information WHERE usersid = ?";
$stmt_email = $conn->prepare($sql_email);
if (!$stmt_email) {
    die("SQL Error: " . $conn->error);
}
$stmt_email->bind_param("i", $usersid);
$stmt_email->execute();
$result_email = $stmt_email->get_result();
$email = ($result_email->num_rows > 0) ? $result_email->fetch_assoc()['email'] : '';

// ✅ Fetch checkout courses with status = 2
$sql = "SELECT * FROM checkout_course WHERE usersid = ? AND status = 2";
$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("SQL Error: " . $conn->error);
}
$stmt->bind_param("i", $usersid);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    echo "<script>alert('❌ No pending checkout found with payment_status = 2.'); window.location.href='IndexCourseDetails.php';</script>";
    exit();
}

$checkoutData = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Checkout</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-5">
    <h2 class="text-center">Order Summary</h2>
    <div class="card mx-auto mt-3" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title">Order Details</h5>
            <p><strong>Order ID:</strong> <span id="orderId"><?php echo htmlspecialchars($checkoutData['order_id']); ?></span></p>
            <input type="hidden" id="course_id" name="course_id" value="<?php echo htmlspecialchars($checkoutData['course_id']); ?>">
            <p><strong>Course:</strong> <span id="courseName"><?php echo htmlspecialchars($checkoutData['course_name']); ?></span></p>
            <p><strong>Instructor:</strong> <span id="instructor"><?php echo htmlspecialchars($checkoutData['course_instructor']); ?></span></p>
            <p><strong>Price:</strong> $<span id="coursePrice"><?php echo htmlspecialchars($checkoutData['course_price']); ?></span></p>
        </div>
    </div>

    <div class="card mx-auto mt-3" style="max-width: 500px;">
        <div class="card-body">
            <h5 class="card-title">Payment Details</h5>
            <form id="paymentForm">
                <input type="hidden" id="usersid" value="<?php echo $usersid; ?>">
                <div class="mb-3">
                    <label for="fullname" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="fullname" value="<?php echo htmlspecialchars($fullname); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" value="<?php echo htmlspecialchars($email); ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="cardNumber" class="form-label">Card Number</label>
                    <input type="text" class="form-control" id="cardNumber" placeholder="Enter your card number" required>
                </div>
                <div class="mb-3">
                    <label for="expiryDate" class="form-label">Expiry Date</label>
                    <input type="month" class="form-control" id="expiryDate" required>
                </div>
                <div class="mb-3">
                    <label for="cvv" class="form-label">CVV</label>
                    <input type="text" class="form-control" id="cvv" placeholder="123" required>
                </div>
                <button type="submit" id="payNow" class="btn btn-primary w-100">Pay Now</button>
            </form>
        </div>
    </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function () {
    $('#paymentForm').submit(function (e) {
        e.preventDefault();

        let formData = {
            usersid: $('#usersid').val().trim(),
            order_id: $('#orderId').text().trim(),
            course_id: $('#course_id').val().trim(),
            course_name: $('#courseName').text().trim(),
            instructor: $('#instructor').text().trim(),
            course_price: $('#coursePrice').text().trim(),
            fullname: $('#fullname').val().trim(),
            email: $('#email').val().trim(),
            cardNumber: $('#cardNumber').val().trim(),
            expiryDate: $('#expiryDate').val().trim(),
            cvv: $('#cvv').val().trim()
        };

        if (!formData.cardNumber || !formData.expiryDate || !formData.cvv) {
            alert("❌ Please complete all payment fields.");
            return;
        }

        // ✅ Mask card number before sending to the server
        formData.cardNumber = formData.cardNumber.slice(-4); 

        $.ajax({
            type: "POST",
            url: "CourseCheckoutFunction.php",
            data: formData,
            success: function (response) {
                alert(response);
                if (response.includes("✅ Payment Successful")) {
                    setTimeout(function () {
                        window.location.href = "CoursePaymentCkFunc.php";
                    }, 2000);
                }
            },
            error: function () {
                alert("❌ Error submitting payment. Please try again.");
            }
        });
    });
});
</script>
</body>
</html>
