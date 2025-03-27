<!-- <?php
include_once("newincludes/newconfig.php");

$lesson_id = $_GET['lesson_id'] ?? '';

$lessons = [];

if (!empty($lesson_id)) {
    // ✅ Validate lesson_id if provided
    if (!is_numeric($lesson_id) || $lesson_id <= 0) {
        echo json_encode(['error' => '❌ Invalid lesson ID']);
        exit;
    }

    $sql = "SELECT lesson_id, lesson_name, description, video_id FROM lessons WHERE lesson_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $lesson_id);
} else {
    // ✅ Fetch all lessons if no lesson_id is provided
    $sql = "SELECT lesson_id, lesson_name, description, video_id FROM lessons";
    $stmt = $conn->prepare($sql);
}

$stmt->execute();
$result = $stmt->get_result();

// ✅ Check if lessons exist
if ($result->num_rows === 0) {
    echo json_encode(['error' => '❌ No lessons found']);
    exit;
}

// ✅ Fetch lessons
while ($row = $result->fetch_assoc()) {
    $lessons[] = [
        'lesson_id'    => $row['lesson_id'],
        'lesson_name'  => $row['lesson_name'],
        'description'  => $row['description'],
        'video_id'     => $row['video_id']
    ];
}

// ✅ Return the lessons data as JSON
echo json_encode($lessons);
?> -->
