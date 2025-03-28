<?php
session_start();
require_once 'includes/config.php'; // Include database connection

if (!isset($_SESSION['authenticated'])) {
    echo "<script>alert('Session expired! Please log in again.'); window.location.href='../LOGIN/LoginAccount.php';</script>";
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Guest';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Navigators Club Home</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-light bg-light shadow-sm">
    <div class="container">
        <a class="navbar-brand fw-bold" href="#">Navigators Club</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarNav">
            <ul class="navbar-nav">
                <?php if (isset($_SESSION['authenticated'])): ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-danger text-white px-3" href="includes/Logout.php">Logout</a>
                    </li>
                <?php else: ?>
                    <li class="nav-item">
                        <a class="nav-link btn btn-primary text-white px-3" href="LOGIN/LoginAccount.php">Login</a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Header Section -->
<header class="py-5 bg-primary text-white">
    <div class="container text-center">
        <div class="row align-items-center">
            <div class="col-lg-6">
                <h1 class="fw-bold">Grow Your Skills to Advance Your Career Path</h1>
                <p class="lead">Education is where learning begins but never ends.</p>
                <a href="<?= isset($_SESSION['stu_id']) ? 'Users/Profile.php' : 'LOGIN/LoginAccount.php' ?>" class="btn btn-light text-primary btn-lg">
                    <?= isset($_SESSION['stu_id']) ? 'Visit Profile' : 'Get Started' ?>
                </a>
            </div>
            <div class="col-lg-6">
                <img src="Img/header.svg" alt="Imperial Academy" class="img-fluid">
            </div>
        </div>
    </div>
</header>

<section class="py-5">
    <div class="container">
        <h2 class="text-center fw-bold mb-4">Popular Courses</h2>
        <div class="row g-4">
            <?php
            $sql = "SELECT * FROM courses ORDER BY RAND() LIMIT 6";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0):
                while ($row = $result->fetch_assoc()): 
                    // Define the correct image path
                    $imagePath = '../Images/CourseImages/Screenshot (3).png' . basename($row['course_image']);
                    
                    // Check if the file exists, otherwise set a default image
                    if (!file_exists($imagePath) || empty($row['course_image'])) {
                        $imagePath = '../Images/CourseImages/default.png'; // Default fallback image
                    }
            ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100 border-0 shadow-sm">
                            <a href="IndexCourseDetails.php?course_id=<?= urlencode($row['course_id']) ?>" class="text-decoration-none">
                                <img src="<?= htmlspecialchars($imagePath) ?>" 
                                     class="card-img-top img-fluid" 
                                     alt="<?= htmlspecialchars($row['course_name']) ?>">
                                <div class="card-body text-center">
                                    <h5 class="fw-bold text-dark"><?= htmlspecialchars($row['course_name']) ?></h5>
                                    <h6 class="text-success">$<?= htmlspecialchars($row['course_price']) ?></h6>
                                    <a href="IndexCourseDetails.php?course_id=<?= urlencode($row['course_id']) ?>" 
                                       class="btn btn-primary mt-3 w-100">
                                        Learn More
                                    </a>
                                </div>
                            </a>
                        </div>
                    </div>
            <?php endwhile; else: ?>
                <p class="text-center text-muted">No courses available at the moment.</p>
            <?php endif; ?>
        </div>
    </div>
</section>




<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>




