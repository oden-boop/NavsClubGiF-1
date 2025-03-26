<?php
include("includes/config.php");

// Fetch courses from course_cart with status 'added_to_cart'
$query = "SELECT course_id, cart_id FROM course_cart WHERE status = ?";
$stmt = $conn->prepare($query);

if (!$stmt) {
    die("SQL Error: " . $conn->error);
}

$status = "added_to_cart";
$stmt->bind_param("s", $status);
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
    <h2 class="text-center">My Cart</h2>
    
    <div class="row">
        <?php if ($result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php
                // Fetch course details from courses table
                $course_id = $row['course_id'];

                // DEBUG: Check if course_id exists
                if (!$course_id) {
                    echo "<p class='text-danger'>Error: Missing course_id in course_cart!</p>";
                    continue; // Skip this row
                }

                $course_query = "SELECT course_name, course_lessons,course_price,course_instructor  FROM courses WHERE course_id = ?";
                $course_stmt = $conn->prepare($course_query);
                
                if ($course_stmt) {
                    $course_stmt->bind_param("i", $course_id);
                    $course_stmt->execute();
                    $course_result = $course_stmt->get_result();
                    $course = $course_result->fetch_assoc();
                } else {
                    echo "<p class='text-danger'>SQL Error: " . $conn->error . "</p>";
                    continue; // Skip this row
                }

                // Check if course exists
                if (!$course) {
                    echo "<p class='text-warning'>Warning: No matching course found for course_id $course_id.</p>";
                    continue; // Skip this row
                }
                ?>

                <div class="col-md-4 col-sm-6 col-12 mb-4">
                    <div class="card shadow">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($course['course_name']); ?></h5>
                            <p class="card-text">Lessons: <?php echo htmlspecialchars($course['course_lessons']); ?></p>
                            <p class="card-text">Instructor: <?php echo htmlspecialchars($course['course_instructor']); ?></p>
                            <p class="card-text"><strong>Price: $<?php echo number_format($course['course_price'], 2); ?></strong></p>
                            <a href="checkout.php?cart_id=<?php echo $row['cart_id']; ?>" class="btn btn-primary w-100">Proceed to Payment</a>
                        </div>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <div class="col-12 text-center">
                <p>No courses in cart.</p>
            </div>
        <?php endif; ?>
    </div>
</div>

</body>
</html>
