<?php
session_start();
require_once 'includes/config.php'; // Database connection

// ✅ Check if the user is logged in
if (!isset($_SESSION['authenticated'])) {
    echo "<script>alert('Session expired! Please log in again.'); window.location.href='../LOGIN/LoginAccount.php';</script>";
    exit();
}

$fullname = $_SESSION['fullname'] ?? 'Guest';

// ✅ Validate & Sanitize `course_id`
if (!isset($_GET['course_id']) || !ctype_digit($_GET['course_id'])) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid course ID.</div>");
}

$course_id = intval($_GET['course_id']);

// ✅ Fetch course details
$sql = "SELECT course_name FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Imperial College - Course Lessons</title>

    <!-- ✅ Stylesheets -->
    <link rel="stylesheet" href="CSS/watchcourse.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">

    <!-- ✅ jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        /* ✅ Custom Styles */
        .header-bar {
            height: 120px;
            background: #343a40;
            padding: 15px;
            color: white;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .lesson-table {
            width: 90%;
            margin: auto;
            background: white;
            border-radius: 8px;
            padding: 15px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
    </style>
</head>
<body>

    <!-- ✅ Course Header -->
    <div class="header-bar">
        <h4>Course Name: <?= htmlspecialchars($course['course_name'] ?? 'Course not found') ?></h4>
        <a href="MyCourses.php" class="btn btn-danger">Back to My Courses</a>
    </div>

    <div class="col-sm-9 mt-5 m-auto">
        <p class="bg-dark text-white p-2">List of Lessons</p>

        <?php
        // ✅ Fetch lessons under the course
        $sql = "SELECT lesson_id, lesson_name FROM course_lessons WHERE course_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $course_id);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
        ?>
            <table class="table lesson-table">
                <thead>
                    <tr>
                        <th class="text-dark fw-bolder">Lesson Name</th>
                        <th class="text-dark fw-bolder">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()) { ?>
                        <tr>
                            <td class="text-dark fw-bolder"><?= htmlspecialchars($row['lesson_name']) ?></td>
                            <td>
                                <a href="WatchCourseEnrolled.php?lesson_id=<?= $row['lesson_id'] ?>" 
                                   class="btn btn-info">View</a>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        <?php
        } else {
            echo "<p class='text-dark p-2 fw-bolder'>Lessons Not Found.</p>";
        }
        $stmt->close();
        ?>
    </div>

</body>
</html>
