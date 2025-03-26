<?php
// ✅ Database connection
$host = 'localhost';
$db = 'navsclubs';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die(json_encode(['error' => '❌ Connection failed: ' . $conn->connect_error]));
}

// ✅ Fetch sections ordered by position
$sql = "SELECT section_id AS section_id, section_name, position FROM course_sections ORDER BY position ASC";
$result = $conn->query($sql);

$sections = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $sections[] = [
            'section_id' => $row['section_id'],    // ✅ Include section_id
            'section_name' => $row['section_name'],
            'position' => $row['position']
        ];
    }
    echo json_encode($sections);
} else {
    echo json_encode([]);
}

$conn->close();
?>
