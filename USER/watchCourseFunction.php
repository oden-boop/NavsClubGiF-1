<?php
session_start();
require_once 'includes/config.php'; // Include database connection

// Check if the user is authenticated
if (!isset($_SESSION['authenticated'])) {
    echo "<script>alert('Session expired! Please log in again.'); window.location.href='../LOGIN/LoginAccount.php';</script>";
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Guest';

$course = [];
$lessons = [];
$firstLesson = "";

// Check if course_id is provided and valid
if (isset($_GET['course_id']) && is_numeric($_GET['course_id'])) {
    $course_id = intval($_GET['course_id']);

    // Fetch course details
    $stmt = $conn->prepare("SELECT * FROM courses WHERE course_id=?");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $course = $result->fetch_assoc();
    $stmt->close();

    // Fetch lessons
    $stmt = $conn->prepare("SELECT * FROM course_lessons WHERE course_id=? ORDER BY lesson_id ASC");
    $stmt->bind_param("i", $course_id);
    $stmt->execute();
    $result = $stmt->get_result();
    
    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }
    
    // Set the first lesson to display initially
    if (!empty($lessons)) {
        $firstLesson = htmlspecialchars($lessons[0]['lesson_link']);
    }

    $stmt->close();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Imperial College - Watch Course</title>
    <link rel="stylesheet" href="CSS/watchcourse.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
</head>
<body>

<!-- Header -->
<div style="height: 120px;" class="container-fluid bg-dark p-2">
    <h4 class="text-white">Course Name: <?php echo htmlspecialchars($course['course_name'] ?? 'Unknown'); ?></h4>
    <a href="MyCourse.php" class="btn fw-bolder btn-danger float-start mt-3">Back to My Course</a>
</div>

<!-- Content -->
<div class="container-fluid">
    <div class="row">
        <!-- Lessons Sidebar -->
        <div class="col-sm-3 border-right">
            <br><br>
            <h4 class="text-center">Lessons</h4>
            <ul class="nav flex-column" id="playlist">
                <?php if (!empty($lessons)): ?>
                    <?php foreach ($lessons as $lesson): ?>
                        <li class="nav-item border-bottom py-2"
                            data-url="https://www.youtube.com/embed/<?php echo htmlspecialchars($lesson['lesson_link']); ?>"
                            style="cursor:pointer;">
                            <?php echo htmlspecialchars($lesson['lesson_name']); ?>
                        </li>
                    <?php endforeach; ?>
                <?php else: ?>
                    <li class="nav-item border-bottom py-2">No lessons found.</li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Video Player -->
        <div class="col-sm-8">
            <br><br>
            <iframe id="videoarea" width="560" height="315"
                    src="https://www.youtube.com/embed/<?php echo $firstLesson; ?>"
                    allowfullscreen class="mt-5 ml-5"></iframe>

            <button id="nextBtn" class="btn btn-primary mt-3">Next</button>
            
            <br>
            <h6 style="width: 600px;"><?php echo htmlspecialchars($course['course_desc'] ?? 'No description available.'); ?></h6>
            <br><br>
        </div>
    </div>
</div>

<script>
$(document).ready(function() {
    let lessons = $("#playlist li");
    let currentIndex = 0;

    // Load first lesson automatically
    if (lessons.length > 0) {
        $("#videoarea").attr("src", lessons.eq(0).data("url"));
        currentIndex = 0;
    }

    // Play selected lesson
    $("#playlist li").on("click", function() {
        $("#videoarea").attr("src", $(this).data("url"));
        currentIndex = $(this).index();
    });

    // Next button functionality
    $("#nextBtn").on("click", function() {
        if (currentIndex < lessons.length - 1) {
            currentIndex++;
            let nextLesson = lessons.eq(currentIndex).data("url");
            $("#videoarea").attr("src", nextLesson);
        } else {
            alert("You have completed all lessons.");
        }
    });
});
</script>

</body>
</html>
