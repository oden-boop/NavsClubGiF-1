<?php
require_once 'newincludes/newconfig.php';

// ✅ Sanitize and validate inputs
$course_id = $_POST['course_id'] ?? '';
$course_name = $_POST['course_name'] ?? '';
$lesson_name = $_POST['lesson_name'] ?? '';
$video_id = $_POST['video_id'] ?? '';
$description = $_POST['lesson_description'] ?? '';
$thumbnail = $_POST['thumbnail'] ?? '';

// ✅ Debug: Print received data
echo "<pre>";
echo "Course ID: $course_id\n";
echo "Course Name: $course_name\n";
echo "Lesson Name: $lesson_name\n";
echo "Video ID: $video_id\n";
echo "Description: $description\n";
echo "Thumbnail: $thumbnail\n";
echo "</pre>";

// ✅ Check for missing fields
if (empty($course_id) || empty($course_name) || empty($lesson_name) || empty($video_id)) {
    echo "❌ Missing required fields.";
    exit;
}

try {
    $conn->begin_transaction();

    // ✅ Insert into lessons (without lesson_id and no link table)
    $stmt = $conn->prepare("
        INSERT INTO lessons (course_id, course_name, lesson_name, video_id, description, thumbnail)
        VALUES (?, ?, ?, ?, ?, ?)
    ");

    if (!$stmt) {
        echo "❌ Prepare failed: " . $conn->error;
        exit;
    }

    $stmt->bind_param("isssss", $course_id, $course_name, $lesson_name, $video_id, $description, $thumbnail);

    if (!$stmt->execute()) {
        throw new Exception("❌ Insert failed: " . $stmt->error);
    }

    $conn->commit();
    echo "✅ Lesson Inserted Successfully!";
    
} catch (Exception $e) {
    $conn->rollback();
    echo "❌ Error: " . $e->getMessage();
} finally {
    $stmt->close();
    $conn->close();
}
?>