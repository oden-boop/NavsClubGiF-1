<?php
include_once("newincludes/newconfig.php");

// ✅ Verify course_id
$course_id = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if (!$course_id) {
    echo "<p>❌ Missing course ID.</p>";
    exit;
}

// ✅ Fetch course and section data
$sql = "
SELECT 
    c.course_id, 
    c.course_name, 
    cs.section_id, 
    cs.section_name, 
    cs.position
FROM courses c
LEFT JOIN course_sections cs ON c.course_id = cs.course_id
WHERE c.course_id = ?
ORDER BY cs.position ASC";  // ✅ Ordered by position

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $course_id);
$stmt->execute();
$result = $stmt->get_result();

// ✅ Check if course exists
if ($result->num_rows === 0) {
    echo "<p>❌ No course or sections found with the given ID.</p>";
    exit;
}

// ✅ Prepare the result
$courseData = [];

while ($row = $result->fetch_assoc()) {
    $courseData[] = [
        'course_id'     => $row['course_id'],
        'course_name'   => $row['course_name'],
        'section_id'    => $row['section_id'],
        'section_name'  => $row['section_name'],
        'position'      => $row['position']
    ];
}

// ✅ Return the result as JSON
echo json_encode($courseData);
?>
