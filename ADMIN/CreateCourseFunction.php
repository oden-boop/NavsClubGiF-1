<?php
include_once("newincludes/newconfig.php");

function sanitizeInput($conn, $input) {
    return htmlspecialchars(strip_tags(mysqli_real_escape_string($conn, trim($input))));
}

function insertCourse($conn) {
    $c_name = sanitizeInput($conn, $_POST['course_name'] ?? '');
    $c_desc = sanitizeInput($conn, $_POST['course_desc'] ?? '');
    $c_price = sanitizeInput($conn, $_POST['course_price'] ?? '');
    $c_level = sanitizeInput($conn, $_POST['course_level'] ?? '');

    // Handle image directly into DB
    if (!isset($_FILES['course_img']) || $_FILES['course_img']['error'] !== UPLOAD_ERR_OK) {
        return '<div class="alert alert-danger">Invalid or missing image!</div>';
    }

    $file = $_FILES['course_img'];
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif'];
    $maxSize = 2 * 1024 * 1024;  // 2 MB

    $fileType = mime_content_type($file['tmp_name']);
    $fileSize = $file['size'];

    if (!in_array($fileType, $allowedTypes) || $fileSize > $maxSize) {
        return '<div class="alert alert-danger">Invalid file type or size!</div>';
    }

    $imageData = file_get_contents($file['tmp_name']);

    if (empty($c_name) || empty($c_desc) || empty($c_price) || empty($c_level) || !$imageData) {
        return '<div class="alert alert-warning">All fields are required!</div>';
    }

    $stmt = $conn->prepare("
        INSERT INTO courses (course_name, course_desc, course_price, course_level, course_image) 
        VALUES (?, ?, ?, ?, ?)
    ");
    
    $stmt->bind_param("ssdss", $c_name, $c_desc, $c_price, $c_level, $imageData);

    if ($stmt->execute()) {
        echo "<script>setTimeout(()=>{window.location.href='CreateCourse.php';},300);</script>";
        return '<div class="alert alert-success">Course Added Successfully!</div>';
    } else {
        return '<div class="alert alert-danger">Error: ' . $conn->error . '</div>';
    }

    $stmt->close();
}

function fetchCourses($conn) {
    $sql = "SELECT course_id, course_name, course_price, course_level, course_image FROM courses";
    $result = $conn->query($sql);

    return ($result->num_rows > 0) ? $result : false;
}
?>
