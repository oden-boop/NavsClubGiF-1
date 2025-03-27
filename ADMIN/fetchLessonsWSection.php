<?php
include_once("newincludes/newconfig.php");

header('Content-Type: application/json');

// ✅ Fetch and sanitize input
$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$section_id || !$course_id) {
    echo json_encode(['error' => '❌ Missing section or course ID']);
    exit;
}

try {
    // ✅ Fetch lessons with section details
    $stmt = $conn->prepare("
        SELECT 
            l.lesson_id, 
            l.lesson_name, 
            l.video_id, 
            l.description,   -- Ensure column exists
            l.thumbnail,
            cs.section_name,
            cs.position
        FROM lessons l
        INNER JOIN course_sections cs ON l.section_id = cs.section_id
        WHERE l.section_id = ? AND l.course_id = ?
    ");

    $stmt->bind_param('ii', $section_id, $course_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $lessons = [];

    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }

    $stmt->close();
    $conn->close();

    echo json_encode($lessons);
} catch (Exception $e) {
    echo json_encode(['error' => '❌ Server error: ' . $e->getMessage()]);
}
?>
