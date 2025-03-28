<?php
require 'newincludes/newconfig.php'; // Database connection

header('Content-Type: application/json'); // Set content type for AJAX response

try {
    // Fetch all sections from the database
    $sql = "SELECT section_id, section_name, position FROM course_sections ORDER BY position ASC";
    $result = $conn->query($sql);

    if ($result === false) {
        // Error executing query
        throw new Exception("Error executing query: " . $conn->error);
    }

    // Check if there are any sections in the result
    if ($result->num_rows > 0) {
        $sections = [];
        while ($row = $result->fetch_assoc()) {
            $sections[] = $row; // Add each section to the array
        }

        // Send response with the sections
        echo json_encode([
            'success' => true,
            'message' => 'Sections fetched successfully!',
            'data' => $sections
        ]);
    } else {
        // No sections found
        echo json_encode([
            'success' => false,
            'message' => 'No sections found in the database.'
        ]);
    }

} catch (Exception $e) {
    // Catch any exception and return an error message
    echo json_encode([
        'success' => false,
        'message' => 'An error occurred: ' . $e->getMessage()
    ]);
} finally {
    // Close the database connection
    $conn->close();
}
?>
