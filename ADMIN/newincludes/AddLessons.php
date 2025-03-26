<?php
include_once("newconfig.php");

// ✅ Handle Form Submission
function handleLessonForm($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['lessonSubmitBtn'])) {

        // ✅ Field Validation
        $requiredFields = ['lesson_name', 'course_id', 'course_name', 'video_id', 'lesson_image'];
        foreach ($requiredFields as $field) {
            if (empty($_POST[$field])) {
                return '<div class="alert alert-warning col-sm-6 ml-5 mt-2">All Fields Required</div>';
            }
        }

        // ✅ Sanitize and Assign Fields
        $lesson_name = htmlspecialchars(trim($_POST['lesson_name']));
        $course_id = intval($_POST['course_id']);  // Ensure course_id is integer
        $course_name = htmlspecialchars(trim($_POST['course_name']));
        $lesson_video_id = htmlspecialchars(trim($_POST['video_id']));  // Use `video_id`
        $lesson_image = htmlspecialchars(trim($_POST['lesson_image']));

        // ✅ SQL Insert Query
        $sql = "INSERT INTO course_lessons (lesson_name, lesson_video_id, course_id, course_name, lesson_image) 
                VALUES (?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            return '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Prepare failed: ' . htmlspecialchars($conn->error) . '</div>';
        }

        // ✅ Bind parameters properly
        $stmt->bind_param("ssiis", $lesson_name, $lesson_video_id, $course_id, $course_name, $lesson_image);

        // ✅ Execute & Handle Results
        if ($stmt->execute()) {
            $stmt->close();
            echo '<meta http-equiv="refresh" content="2;">';  // Auto-refresh
            return '<div class="alert alert-success col-sm-6 ml-5 mt-2">✅ Lesson Added Successfully</div>';
        } else {
            return '<div class="alert alert-danger col-sm-6 ml-5 mt-2">❌ Lesson Addition Failed: ' . htmlspecialchars($stmt->error) . '</div>';
        }
    }
    return '';
}
?>
