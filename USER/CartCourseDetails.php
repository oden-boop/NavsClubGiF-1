<?php
include("includes/config.php");

// Fetch course details in one query
$courseQuery = "
    SELECT c.course_id, c.course_name, c.course_instructor, c.course_price, c.course_desc, c.course_level
    FROM course_cart cc
    JOIN courses c ON cc.course_id = c.course_id
    WHERE cc.status = 'added_to_cart'
";

$courseResult = $conn->query($courseQuery);

// Check for errors
if (!$courseResult) {
    die("SQL Error: " . $conn->error);
}

// Fetch courses
$courses = $courseResult->fetch_all(MYSQLI_ASSOC);

// Close connection
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<div class="container mt-5">
    <h2 class="text-center mb-4">Courses in Your Cart</h2>

    <?php if (empty($courses)): ?>
        <p class="text-center text-muted">No courses added to cart yet.</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($courses as $row): ?>
                <div class="col">
                    <div class="card shadow-sm h-100">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['course_name']); ?></h5>
                            <h6 class="text-muted">By: <?php echo htmlspecialchars($row['course_instructor']); ?></h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['course_desc'])); ?></p>
                            <p><strong>Level:</strong> <?php echo htmlspecialchars($row['course_level']); ?></p>
                            <p class="fw-bold text-primary">$<?php echo number_format($row['course_price'], 2); ?></p>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

</body>
</html>
