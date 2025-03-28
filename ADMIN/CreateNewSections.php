<?php
require 'newincludes/newconfig.php'; // Database connection

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $section_name = $_POST['section_name'];
    
    // Step 1: Get the highest current section position
    $query = "SELECT MAX(position) AS max_position FROM course_sections";
    $result = $conn->query($query);
    
    if ($result && $row = $result->fetch_assoc()) {
        $section_position = $row['max_position'] + 1; // Increment the position by 1
    } else {
        $section_position = 1; // If no sections exist, start with position 1
    }
    
    // Step 2: Insert the new section into the database with the incremented position
    $stmt = $conn->prepare("INSERT INTO course_sections (section_name, position) VALUES (?, ?)");
    $stmt->bind_param("si", $section_name, $section_position);
    
    if ($stmt->execute()) {
        echo json_encode(["success" => true, "message" => "Section added successfully!"]);
    } else {
        echo json_encode(["success" => false, "message" => "Failed to add section!"]);
    }

    $stmt->close();
    $conn->close();
}
?>
