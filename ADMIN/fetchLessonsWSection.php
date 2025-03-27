<?php
include_once("newincludes/newconfig.php");

header('Content-Type: application/json');

// ✅ Fetch and sanitize input
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$course_id) {
    echo json_encode(['error' => '❌ Missing course ID']);
    exit;
}

try {
    // ✅ Fetch sections for the specific course
    $stmt = $conn->prepare("
        SELECT 
            cs.section_id, 
            cs.section_name, 
            cs.position
        FROM course_sections cs
        INNER JOIN courses c ON c.course_id = cs.course_id  -- ✅ FIXED JOIN
        WHERE c.course_id = ?
    ");

    $stmt->bind_param('i', $course_id);
    $stmt->execute();

    $result = $stmt->get_result();
    $sections = [];

    while ($row = $result->fetch_assoc()) {
        $sections[] = $row;
    }

    // ✅ Close statement and connection
    $stmt->close();
    $conn->close();

    // ✅ Return JSON response
    echo json_encode($sections, JSON_PRETTY_PRINT);

} catch (Exception $e) {
    echo json_encode(['error' => '❌ Server error: ' . $e->getMessage()]);
}
?>
