<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'navsclubs');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Allowed file extensions and MIME types
$allowed_extensions = ['zip', 'pdf', 'xlsx', 'jpg', 'jpeg', 'png', 'gif'];
$allowed_mime_types = [
    'application/zip',
    'application/pdf',
    'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
    'image/jpeg',
    'image/png',
    'image/gif'
];

// Set max file size (ZIP: 50MB, Others: 2MB)
$max_file_sizes = [
    'zip' => 50 * 1024 * 1024, // 50MB
    'default' => 2 * 1024 * 1024 // 2MB
];

$response = ['success' => false, 'error' => 'No file uploaded!'];

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_FILES['files'])) {
    $uploadedFiles = $_FILES['files'];
    
    // Handle multiple file uploads
    $successCount = 0;
    $errors = [];

    for ($i = 0; $i < count($uploadedFiles['name']); $i++) {
        $file = [
            'name' => $uploadedFiles['name'][$i],
            'tmp_name' => $uploadedFiles['tmp_name'][$i],
            'size' => $uploadedFiles['size'][$i],
            'type' => $uploadedFiles['type'][$i]
        ];

        $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        $max_file_size = $max_file_sizes[$extension] ?? $max_file_sizes['default'];

        if (!in_array($extension, $allowed_extensions)) {
            $errors[] = "Invalid file type: $extension";
            continue;
        }

        if ($file['size'] > $max_file_size) {
            $errors[] = "File too large: {$file['name']} exceeds limit.";
            continue;
        }

        $result = storeFileInDatabase($conn, $file);
        if ($result['success']) {
            $successCount++;
        } else {
            $errors[] = $result['error'];
        }
    }

    if ($successCount > 0) {
        $response = ['success' => true, 'message' => "$successCount file(s) uploaded successfully."];
    } else {
        $response = ['success' => false, 'error' => implode(", ", $errors)];
    }
}

mysqli_close($conn);
echo json_encode($response);

/**
 * Function to store a file in the database.
 */
function storeFileInDatabase($conn, $file) {
    $filename = $file['name'];
    $size = $file['size'];
    $mime_type = mime_content_type($file['tmp_name']);
    $data = file_get_contents($file['tmp_name']);
    
    $sql = "INSERT INTO files (name, size, type, data) VALUES (?, ?, ?, ?)";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "sisb", $filename, $size, $mime_type, $null);
    mysqli_stmt_send_long_data($stmt, 3, $data);
    $result = mysqli_stmt_execute($stmt);

    if ($result) {
        mysqli_stmt_close($stmt);
        return ['success' => true, 'message' => "File '$filename' uploaded successfully."];
    } else {
        return ['success' => false, 'error' => 'Database error: ' . mysqli_error($conn)];
    }
}
?>