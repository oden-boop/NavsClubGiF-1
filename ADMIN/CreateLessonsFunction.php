<?php
// ✅ Database connection
$host = 'localhost';
$db = 'navsclubs';
$user = 'root';
$pass = '';
$conn = new mysqli($host, $user, $pass, $db);

if ($conn->connect_error) {
    die('❌ Connection failed: ' . $conn->connect_error);
}

// ✅ Capture form data
$section_name = $_POST['section_name'] ?? '';

// ✅ Auto-calculate the next position
$check_sql = "SELECT COUNT(*) AS total FROM course_sections";
$result = $conn->query($check_sql);
$row = $result->fetch_assoc();
$next_position = $row['total'] + 1;  // Increment position

// ✅ Insert the new section
$insert_sql = "INSERT INTO course_sections (section_name, position) VALUES (?, ?)";
$stmt = $conn->prepare($insert_sql);
$stmt->bind_param("si", $section_name, $next_position);

if ($stmt->execute()) {
    echo "✅ Section added successfully.";
} else {
    echo "❌ Error: " . $stmt->error;
}

$stmt->close();
$conn->close();
?>
