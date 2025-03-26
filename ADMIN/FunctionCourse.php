<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'navsclubs');

if (!$conn) {
    die(json_encode(["success" => false, "error" => "Connection failed: " . mysqli_connect_error()]));
}

$sql = "SELECT id, name, size, type FROM files";
$result = mysqli_query($conn, $sql);

$files = [];
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $files[] = [
            "name" => $row['name'],
            "size" => round($row['size'] / 1024, 2) . " KB",
        ];
    }
}

mysqli_close($conn);

echo json_encode(["success" => true, "files" => $files]);
?>
