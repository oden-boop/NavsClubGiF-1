<?php
session_start();
include("includes/config.php");

// Check if the user is logged in
if (!isset($_SESSION['usersid'])) {
    header("Location: NavsClubGIF/NavsClubGIF/LOGIN/LoginAccount.php");
    exit();
}

$usersid = $_SESSION['usersid'];

// Fetch user details (full name, email)
$user_query = "SELECT fullname, email FROM personal_information WHERE usersid = ?";
$user_stmt = $conn->prepare($user_query);
$user_stmt->bind_param("i", $usersid);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Avoid errors if no user data found
$fullname = $user['fullname'] ?? 'Guest';
$email = $user['email'] ?? 'No Email';

// Fetch courses from cart with a JOIN
$query = "SELECT c.course_id, c.course_name, c.course_lessons, c.course_price, c.course_instructor, cc.cart_id 
          FROM course_cart cc 
          INNER JOIN courses c ON cc.course_id = c.course_id
          WHERE cc.usersid = ? AND cc.status = ?";
$stmt = $conn->prepare($query);
$status = 1; // Status as TINYINT(1) (1 = added to cart, 0 = removed)
$stmt->bind_param("ii", $usersid, $status);
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
                <div class="col-md-4 col-sm-6 col-12 mb-4">
                    <div class="card shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?= htmlspecialchars($row['course_name']) ?></h5>
                            <p class="card-text">Lessons: <?= htmlspecialchars($row['course_lessons']) ?></p>
                            <p class="card-text">Instructor: <?= htmlspecialchars($row['course_instructor']) ?></p>
                            <p class="card-text"><strong>Price: $<?= number_format($row['course_price'], 2) ?></strong></p>

                            <form method="POST" action="FetchAddtoCart.php">
                                <input type="hidden" name="course_id" value="<?= $row['course_id'] ?>">
                                <input type="hidden" name="cart_id" value="<?= $row['cart_id'] ?>">
                                <button type="submit" class="btn btn-primary w-100">Proceed to Payment</button>
                            </form>
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

</body>
</html>
