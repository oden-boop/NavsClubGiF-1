<?php
require_once "newincludes/newconfig.php";

// ✅ Get section_id from the request
$section_id = isset($_GET['section_id']) ? intval($_GET['section_id']) : 0;

if ($section_id <= 0) {
    echo json_encode(['error' => 'Invalid section ID']);
    exit;
}

try {
    // ✅ Query to fetch lessons with section_name and position
    $query = "
        SELECT 
            l.lesson_id,
            l.lesson_name,
            l.video_id,
            s.section_name,
            s.position
        FROM lessons l
        INNER JOIN course_sections s ON l.section_id = s.section_id
        WHERE l.section_id = ?
        ORDER BY s.position ASC, l.lesson_id ASC
    ";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("i", $section_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $lessons = [];

    while ($row = $result->fetch_assoc()) {
        $lessons[] = [
            'lesson_id'     => $row['lesson_id'],
            'lesson_name'   => $row['lesson_name'],
            'video_id'      => $row['video_id'],
            'section_name'  => $row['section_name'],
            'position'      => $row['position']
        ];
    }

    echo json_encode($lessons);

} catch (Exception $e) {
    echo json_encode(['error' => '❌ Failed to fetch lessons: ' . $e->getMessage()]);
}

$stmt->close();
$conn->close();
?>
