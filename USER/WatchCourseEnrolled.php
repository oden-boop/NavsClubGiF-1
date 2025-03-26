<?php
session_start();
require_once 'includes/config.php'; // Secure DB connection

// ✅ Check if user is logged in
if (!isset($_SESSION['authenticated'])) {
    echo "<script>alert('Session expired! Please log in again.'); window.location.href='../LOGIN/LoginAccount.php';</script>";
    exit();
}

// ✅ Validate & Sanitize lesson_id
if (!isset($_GET['lesson_id']) || !ctype_digit($_GET['lesson_id'])) {
    die("<div class='alert alert-danger text-center mt-5'>Invalid lesson ID.</div>");
}

$lesson_id = intval($_GET['lesson_id']); // Convert to integer

// ✅ Convert YouTube link to embed format
function convertToEmbed($url) {
    if (preg_match('/youtu\.be\/([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }
    if (preg_match('/youtube\.com\/watch\?v=([a-zA-Z0-9_-]+)/', $url, $matches)) {
        return "https://www.youtube.com/embed/" . $matches[1];
    }
    return $url; // Return original if no match
}

// ✅ Fetch lesson & course name
$sql = "SELECT cl.lesson_id, cl.lesson_name, cl.lesson_link, cl.course_id, c.course_name
        FROM course_lessons cl
        JOIN courses c ON cl.course_id = c.course_id
        WHERE cl.lesson_id = ?";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $lesson_id);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();
$stmt->close();

if (!$row) {
    die("<div class='alert alert-danger text-center mt-5'>Lesson not found.</div>");
}

// Convert YouTube link
$embed_url = convertToEmbed($row['lesson_link']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Navigati College - Watch Lesson</title>
    
    <!-- ✅ Stylesheets -->
    <link rel="stylesheet" href="CSS/watchcourse.css">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    
    <!-- ✅ jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>

    <style>
        /* ✅ Responsive Video */
        #videoarea {
            width: 100%;
            max-width: 900px;
            height: 500px;
            display: block;
            margin: auto;
        }
        .container-fluid h3 {
            font-size: 24px;
        }
    </style>
</head>
<body>

    <!-- ✅ Navbar -->
    <div class="container-fluid bg-dark p-3">
        <h4 class="text-white text-center">Course: <?= htmlspecialchars($row['course_name']) ?></h4>
    </div>

    <div class="container mt-4">
        <div class="row">
            <div class="col-md-12 text-center">
                <h3 class="mb-4"><?= htmlspecialchars($row['lesson_name']) ?></h3>

                <!-- ✅ Embedded Video (Fixed) -->
                <iframe id="videoarea" 
                    src="<?= htmlspecialchars($embed_url) ?>?controls=1" 
                    allowfullscreen>
                </iframe>

                <!-- ✅ Finish Button -->
                <a href="PlaylistWatch.php?lesson_id=<?= htmlspecialchars($row['lesson_id']) ?>&course_id=<?= htmlspecialchars($row['course_id']) ?>" class="btn btn-danger mt-4">Finish</a>

            </div>
        </div>
    </div>

</body>
</html>
