<?php
// ✅ Database connection
include_once("newincludes/newconfig.php");

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Fetch and sanitize input
    $course_id = $_POST['course_id'] ?? null;
    $section_id = $_POST['section_id'] ?? null;
    $lesson_name = $_POST['lesson_name'] ?? '';
    $video_id = $_POST['video_id'] ?? '';
    $description = $_POST['description'] ?? '';
    $thumbnail = $_POST['thumbnail'] ?? '';
    $position = $_POST['position'] ?? 1; // Default lesson position

    // ✅ Validation: Ensure required fields are provided
    if (!$course_id || !$section_id || empty($lesson_name) || empty($video_id)) {
        echo json_encode(['error' => '❌ Missing required fields']);
        exit;
    }

    try {
        // ✅ Check if section exists in course_sections
        $sectionCheckStmt = $conn->prepare("
            SELECT section_id FROM course_sections WHERE section_id = :section_id
        ");
        $sectionCheckStmt->execute([':section_id' => $section_id]);
        $section = $sectionCheckStmt->fetch(PDO::FETCH_ASSOC);

        if (!$section) {
            echo json_encode(['error' => '❌ Invalid section ID.']);
            exit;
        }

        // ✅ Check if lesson position is already occupied
        $checkStmt = $conn->prepare("
            SELECT 1 FROM lessons WHERE section_id = :section_id AND position = :position
        ");
        $checkStmt->execute([
            ':section_id' => $section_id,
            ':position' => $position
        ]);

        if ($checkStmt->fetch()) {
            echo json_encode(['error' => '❌ A lesson already exists in this position within this section.']);
            exit;
        }

        // ✅ Insert Lesson
        $sql = "
            INSERT INTO lessons (
                course_id, section_id, lesson_name, video_id, 
                description, thumbnail, position
            ) VALUES (
                :course_id, :section_id, :lesson_name, :video_id, 
                :description, :thumbnail, :position
            )
        ";

        $stmt = $conn->prepare($sql);
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $stmt->bindParam(':lesson_name', $lesson_name, PDO::PARAM_STR);
        $stmt->bindParam(':video_id', $video_id, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':thumbnail', $thumbnail, PDO::PARAM_STR);
        $stmt->bindParam(':position', $position, PDO::PARAM_INT);

        if ($stmt->execute()) {
            echo json_encode(['success' => '✅ Lesson inserted successfully at position ' . $position]);
        } else {
            echo json_encode(['error' => '❌ Failed to insert lesson']);
        }

    } catch (PDOException $e) {
        echo json_encode(['error' => '❌ Database error: ' . $e->getMessage()]);
    }
}
?>
