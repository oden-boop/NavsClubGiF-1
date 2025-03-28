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
    <title>Courses in Cart</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        
        .course-card {
            transition: transform 0.3s ease-in-out, box-shadow 0.3s ease-in-out;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0px 10px 20px rgba(0, 0, 0, 0.15);
        }
        
        .card-body h5 {
            color: #007bff;
            font-weight: bold;
        }
        
        .text-muted {
            font-size: 14px;
        }
        
        .price {
            font-size: 18px;
            font-weight: bold;
            color: #28a745;
        }
        
        .no-courses {
            font-size: 18px;
            color: #6c757d;
            text-align: center;
            margin-top: 20px;
        }
    </style>
</head>
<body>

<div class="container py-5">
    <h2 class="text-center mb-4 fw-bold text-primary">Your Cart</h2>

    <?php if (empty($courses)): ?>
        <p class="no-courses">No courses added to cart yet.</p>
    <?php else: ?>
        <div class="row row-cols-1 row-cols-md-3 g-4">
            <?php foreach ($courses as $row): ?>
                <div class="col">
                    <div class="card course-card h-100 shadow-sm">
                        <div class="card-body">
                            <h5 class="card-title"><?php echo htmlspecialchars($row['course_name']); ?></h5>
                            <h6 class="text-muted">Instructor: <?php echo htmlspecialchars($row['course_instructor']); ?></h6>
                            <p class="card-text"><?php echo nl2br(htmlspecialchars($row['course_desc'])); ?></p>
                            <p><strong>Level:</strong> <?php echo htmlspecialchars($row['course_level']); ?></p>
                            <p class="price">$<?php echo number_format($row['course_price'], 2); ?></p>
                            <div class="d-grid">
                                <button class="btn btn-outline-primary">View Course</button>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
