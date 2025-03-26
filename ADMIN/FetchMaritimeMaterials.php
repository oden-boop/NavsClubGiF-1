<?php
// Secure database connection
function connectDB() {
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "navsclubs";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die(json_encode(["error" => "Database connection failed: " . $conn->connect_error]));
    }

    return $conn;
}

// Fetch all files with only id, name, and type
function fetchFiles($conn) {
    $sql = "SELECT id, name, type FROM files";
    $stmt = $conn->prepare($sql);
    $stmt->execute();
    $result = $stmt->get_result();

    $files = $result ? $result->fetch_all(MYSQLI_ASSOC) : [];

    $stmt->close();
    return $files;
}

// Execute functions
$conn = connectDB();
$files = fetchFiles($conn);
$conn->close();

// Output JSON response
header('Content-Type: application/json');
echo json_encode(["files" => $files], JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
?>
