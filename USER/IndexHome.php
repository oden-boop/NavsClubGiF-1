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
    <link rel="stylesheet" href="https://unpkg.com/swiper@8/swiper-bundle.min.css">
</head>
<body>

<header>
    <div class="container header__container">
        <div class="header__left">
            <h1>Grow your Skills to Advance your Career Path</h1>
            <p>Education is where learning begins but never ends.</p>
            <a href="<?= isset($_SESSION['stu_id']) ? 'Users/Profile.php' : 'LOGIN/LoginAccount.php' ?>">
                <button class="button"> <?= isset($_SESSION['stu_id']) ? 'Visit Profile' : 'Get Started' ?> </button>
            </a>
        </div>
        <div class="header__right">
            <img src="Img/header.svg" alt="Imperial Academy">
        </div>
        <?php if (isset($_SESSION['authenticated'])): ?>
            <a href="includes/Logout.php"><button class="button">Logout</button></a>
        <?php endif; ?>
    </div>
</header>


<section class="categories reveal">
    <div class="container categories__container">
        <div class="categories__left">
            <h1>Categories</h1>
            <p>Learn programming languages with quality guidance from Imperial Academy.</p>
        </div>
        <div class="categories__right">
            <?php
            $categories = [
                ['icon' => 'uil uil-android', 'title' => 'App Development', 'desc' => 'Mobile App Development'],
                ['icon' => 'uil uil-browser', 'title' => 'Web Development', 'desc' => 'Building Websites'],
                ['icon' => 'uil uil-brackets-curly', 'title' => 'Back-End Development', 'desc' => 'Server-Side Coding'],
                ['icon' => 'uil uil-pen', 'title' => 'UI/UX Design', 'desc' => 'User Interface & Experience'],
            ];
            foreach ($categories as $cat): ?>
                <article class="category">
                    <span class="category__icon"><i class="<?= $cat['icon'] ?>"></i></span>
                    <h5><?= htmlspecialchars($cat['title']) ?></h5>
                    <p><?= htmlspecialchars($cat['desc']) ?></p>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<section class="courses reveal">
    <h2>Popular Courses</h2>
    <div class="container courses__container">
        <?php
        $sql = "SELECT * FROM courses ORDER BY RAND() LIMIT 6";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()): ?>
                <article class="course">
                    <a href="IndexCourseDetails.php?course_id=<?= urlencode($row['course_id']) ?>">
                        <div class="course_image">
                            <img src="<?= htmlspecialchars(str_replace('..', '.', $row['course_image'])) ?>" alt="<?= htmlspecialchars($row['course_name']) ?>">
                        </div>
                        <div class="course__info">
                            <h3><?= htmlspecialchars($row['course_name']) ?></h3>
                            <h5><?= htmlspecialchars($row['course_instructor']) ?></h5>
                            <h4>$<?= htmlspecialchars($row['course_price']) ?></h4>
                            <button class="button">Learn More</button>
                        </div>
                    </a>
                </article>
        <?php endwhile; else: ?>
            <p>No courses available at the moment.</p>
        <?php endif; ?>
    </div>
</section>

<section class="container testimonials__container mySwiper reveal">
    <h2>Student Reviews</h2>
    <div class="swiper-wrapper">
        <?php
        $sql = "SELECT s.stu_name, s.stu_occ, s.stu_img, f.f_content FROM students AS s 
                JOIN feedback AS f ON s.stu_id = f.stu_id";
        $result = $conn->query($sql);

        if ($result && $result->num_rows > 0):
            while ($row = $result->fetch_assoc()): ?>
                <article class="testimonial swiper-slide">
                    <div class="avatar">
                        <img src="<?= htmlspecialchars(str_replace('..', '.', $row['stu_img'])) ?>" alt="<?= htmlspecialchars($row['stu_name']) ?>">
                    </div>
                    <div class="testimonial__info">
                        <h5><?= htmlspecialchars($row['stu_name']) ?></h5>
                        <small><?= htmlspecialchars($row['stu_occ']) ?></small>
                    </div>
                    <div class="testimonial__body">
                        <p><?= htmlspecialchars($row['f_content']) ?></p>
                    </div>
                </article>
        <?php endwhile; else: ?>
            <p>No reviews available.</p>
        <?php endif; ?>
    </div>
    <div class="swiper-pagination"></div>
</section>

<section id="features" class="reveal">
    <h1>Awesome Features</h1>
    <div class="fea-base">
        <div class="fea-box">
            <i class="uil uil-graduation-cap"></i>
            <h3>Scholarship Facility</h3>
        </div>
        <div class="fea-box">
            <i class="uil uil-trophy"></i>
            <h3>Global Recognition</h3>
        </div>
        <div class="fea-box">
            <i class="uil uil-clipboard-alt"></i>
            <h3>Enroll Course</h3>
        </div>
    </div>
</section>

<script src="https://unpkg.com/swiper@8/swiper-bundle.min.js"></script>
<script>
    var swiper = new Swiper(".mySwiper", {
        slidesPerView: 1,
        spaceBetween: 30,
        pagination: {
            el: ".swiper-pagination",
            clickable: true,
        },
        breakpoints: { 600: { slidesPerView: 3 } }
    });

    function reveal() {
        var reveals = document.querySelectorAll(".reveal");
        for (var i = 0; i < reveals.length; i++) {
            var windowHeight = window.innerHeight;
            var elementTop = reveals[i].getBoundingClientRect().top;
            var elementVisible = 150;
            if (elementTop < windowHeight - elementVisible) {
                reveals[i].classList.add("active");
            } else {
                reveals[i].classList.remove("active");
            }
        }
    }
    window.addEventListener("scroll", reveal);
</script>
</body>
</html>
