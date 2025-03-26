<?php
require_once 'newincludes/newconfig.php';
header('Content-Type: application/json');

$lesson_id = isset($_GET['lesson_id']) ? (int) $_GET['lesson_id'] : 0;

try {
    // ✅ SQL query with correct columns
    $sql = "
        SELECT 
            l.lesson_id AS lesson_id, 
            l.course_id AS course_id, 
            l.lesson_name AS lesson_name, 
            l.description AS lesson_description, 
            l.video_id AS video_id, 
            l.thumbnail AS thumbnail, 
            l.course_name AS course_name
        FROM lessons l
    ";

    // ✅ Filter by lesson_id if provided
    if ($lesson_id > 0) {
        $sql .= " WHERE l.lesson_id = ?";
    }

    $stmt = $conn->prepare($sql);

    if (!$stmt) {
        echo json_encode(['error' => 'Prepare failed: ' . $conn->error]);
        exit;
    }

    // ✅ Bind parameter only if lesson_id is provided
    if ($lesson_id > 0) {
        $stmt->bind_param('i', $lesson_id);
    }

    $stmt->execute();
    $result = $stmt->get_result();

    $lessons = [];

    while ($row = $result->fetch_assoc()) {
        $lessons[] = $row;
    }

    if (!empty($lessons)) {
        echo json_encode($lessons, JSON_PRETTY_PRINT);
    } else {
        echo json_encode(['error' => 'No lessons found']);
    }

    $stmt->close();
    $conn->close();

} catch (Exception $e) {
    echo json_encode(['error' => 'Exception: ' . $e->getMessage()]);
}
?>
