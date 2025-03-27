<?php
// ✅ Database connection
include_once("newincludes/newconfig.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // ✅ Extracting form data from the form submission
    $course_id = $_POST['course_id'] ?? null;           // Course ID (Fetched)
    $course_name = $_POST['course_name'] ?? '';         // Course Name (Fetched)
    $section_id = $_POST['section_id'] ?? null;         // Section ID (Fetched)
    $section_name = $_POST['section_name'] ?? '';       // Section Name (Fetched)
    $section_position = $_POST['section_position'] ?? null; // Section Position (Fetched)
    $lesson_name = $_POST['lesson_name'] ?? '';         // Lesson Name (Manual Input)
    $video_id = $_POST['video_id'] ?? '';               // Video ID (Fetched)
    $description = $_POST['description'] ?? '';         // Description (Fetched)
    $thumbnail = $_POST['thumbnail'] ?? '';             // Thumbnail (Fetched)
    $position = $_POST['position'] ?? 1;                // Lesson Position in Section

    // ✅ Validation: Ensure all required fields are provided
    if (!$course_id || !$section_id || !$section_position || empty($lesson_name) || empty($video_id)) {
        echo json_encode(['error' => '❌ Missing required fields']);
        exit;
    }

    try {
        // ✅ Ensure the section_position is correctly assigned
        $sectionCheckStmt = $conn->prepare("
            SELECT section_id, section_position 
            FROM sections 
            WHERE section_id = :section_id AND section_position = :section_position
        ");
        $sectionCheckStmt->execute([
            ':section_id' => $section_id,
            ':section_position' => $section_position
        ]);
        $section = $sectionCheckStmt->fetch(PDO::FETCH_ASSOC);

        if (!$section) {
            echo json_encode(['error' => '❌ Section position mismatch.']);
            exit;
        }

        // ✅ Check if the lesson position already exists in this section
        $checkStmt = $conn->prepare("
            SELECT 1 
            FROM lessons 
            WHERE section_id = :section_id AND position = :position
        ");
        $checkStmt->execute([
            ':section_id' => $section_id,
            ':position' => $position
        ]);

        if ($checkStmt->fetch()) {
            echo json_encode(['error' => '❌ A lesson already exists in this position within this section.']);
            exit;
        }

        // ✅ Insert Query (Without created_at & duration)
        $sql = "
            INSERT INTO lessons (
                course_id, course_name, section_id, section_name, 
                section_position, lesson_name, video_id, description, 
                thumbnail, position
            ) VALUES (
                :course_id, :course_name, :section_id, :section_name, 
                :section_position, :lesson_name, :video_id, :description, 
                :thumbnail, :position
            )
        ";

        $stmt = $conn->prepare($sql);

        // ✅ Bind Parameters
        $stmt->bindParam(':course_id', $course_id, PDO::PARAM_INT);
        $stmt->bindParam(':course_name', $course_name, PDO::PARAM_STR);
        $stmt->bindParam(':section_id', $section_id, PDO::PARAM_INT);
        $stmt->bindParam(':section_name', $section_name, PDO::PARAM_STR);
        $stmt->bindParam(':section_position', $section_position, PDO::PARAM_INT);
        $stmt->bindParam(':lesson_name', $lesson_name, PDO::PARAM_STR);
        $stmt->bindParam(':video_id', $video_id, PDO::PARAM_STR);
        $stmt->bindParam(':description', $description, PDO::PARAM_STR);
        $stmt->bindParam(':thumbnail', $thumbnail, PDO::PARAM_STR);
        $stmt->bindParam(':position', $position, PDO::PARAM_INT);

        // ✅ Execute Query
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
