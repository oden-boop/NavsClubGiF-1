<?php
session_start();
include("includes/config.php");

// ✅ Check if user is logged in
if (!isset($_SESSION['usersid'])) {
    header("Location: NavsClubGIF/NavsClubGIF/LOGIN/LoginAccount.php");
    exit();
}

$usersid = $_SESSION['usersid'];

// ✅ Fetch user details (full name, email)
$user_query = "SELECT fullname, email FROM personal_information WHERE usersid = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $usersid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

$fullname = $user['fullname'] ?? 'Unknown';
$email = $user['email'] ?? 'Unknown';

// ✅ Fetch courses from cart (only where status = 2)
$query = "SELECT course_id, cart_id FROM course_cart WHERE usersid = ? AND status = 2";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $usersid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">My Cart</h2>
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                $course_id = $row['course_id'];
                
                // ✅ Fetch course details with valid columns
                $course_query = "SELECT course_name, course_price, course_level, course_duration, course_desc 
                                 FROM courses WHERE course_id = ?";
                $course_stmt = $conn->prepare($course_query);
                $course_stmt->bind_param("i", $course_id);
                $course_stmt->execute();
                $course_result = $course_stmt->get_result();
                $course = $course_result->fetch_assoc();
                ?>

                <div class="col-md-4 col-sm-6 col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($course['course_name']); ?></h5>
                            <p class="card-text">Level: <?= htmlspecialchars($course['course_level']); ?></p>
                            <p class="card-text"><strong>Price: $<?= number_format($course['course_price'], 2); ?></strong></p>
                            <p class="card-text"><?= nl2br(htmlspecialchars($course['course_desc'])); ?></p>

                            <!-- ✅ Proceed to checkout -->
                            <button class="btn btn-primary w-100 proceedToCheckout"
                                    data-course-name="<?= htmlspecialchars($course['course_name']); ?>"
                                    data-course-price="<?= number_format($course['course_price'], 2); ?>"
                                    data-fullname="<?= htmlspecialchars($fullname); ?>"
                                    data-email="<?= htmlspecialchars($email); ?>">
                                Proceed to Payment
                            </button>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p class="text-muted">No courses in cart.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

<script>
    document.querySelectorAll(".proceedToCheckout").forEach(button => {
        button.addEventListener("click", function () {
            let courseName = this.getAttribute("data-course-name");
            let coursePrice = this.getAttribute("data-course-price");
            let fullname = this.getAttribute("data-fullname");
            let email = this.getAttribute("data-email");
            let orderId = "ORD_" + Date.now(); // Generate unique order ID

            // ✅ Store data in sessionStorage
            sessionStorage.setItem("course_name", courseName);
            sessionStorage.setItem("course_price", coursePrice);
            sessionStorage.setItem("fullname", fullname);
            sessionStorage.setItem("email", email);
            sessionStorage.setItem("order_id", orderId);

            // ✅ Redirect to checkout page
            window.location.href = "CourseForCheckout.php";
        });
    });
</script>

</body>
</html>
