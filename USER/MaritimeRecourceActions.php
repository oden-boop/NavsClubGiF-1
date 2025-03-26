<?php
// Connect to the database
$conn = mysqli_connect('localhost', 'root', '', 'navsclub');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

// Fetch all files from the database
$sql = "SELECT id, name, size, type, downloads FROM files";
$result = mysqli_query($conn, $sql);

$files = $result ? mysqli_fetch_all($result, MYSQLI_ASSOC) : [];

// Handle file upload
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['save'])) {
    if (isset($_FILES['myfile'])) {
        $filename = $_FILES['myfile']['name'];
        $extension = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
        $file = $_FILES['myfile']['tmp_name'];
        $size = $_FILES['myfile']['size'];
        $mime_type = mime_content_type($file);
        $file_data = file_get_contents($file);

        // Allowed file types
        $allowed_extensions = ['zip', 'pdf', 'docx'];
        $allowed_mime_types = ['application/zip', 'application/pdf', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'];

        if (!in_array($extension, $allowed_extensions) || !in_array($mime_type, $allowed_mime_types)) {
            echo "<script>alert('Invalid file type! Only ZIP, PDF, or DOCX are allowed.'); window.location='index.php';</script>";
            exit;
        } elseif ($size > 1000000) { // Limit to 1MB
            echo "<script>alert('File too large! Maximum allowed size is 1MB.'); window.location='index.php';</script>";
            exit;
        } else {
            // Insert file into the database securely
            $sql = "INSERT INTO files (name, size, type, data, downloads) VALUES (?, ?, ?, ?, 0)";
            $stmt = mysqli_prepare($conn, $sql);
            mysqli_stmt_bind_param($stmt, "sisb", $filename, $size, $mime_type, $null);
            mysqli_stmt_send_long_data($stmt, 3, $file_data); // Store file as BLOB
            $result = mysqli_stmt_execute($stmt);

            if ($result) {
                // Redirect to prevent form resubmission
                header("Location: index.php?upload_success=1");
                exit();
            } else {
                echo "Database error: " . mysqli_error($conn);
            }

            mysqli_stmt_close($stmt);
        }
    } else {
        echo "<script>alert('No file uploaded!'); window.location='index.php';</script>";
    }
}

// Handle file download
if (isset($_GET['file_id'])) {
    $id = intval($_GET['file_id']);

    // Fetch the file from the database
    $sql = "SELECT name, type, data, downloads FROM files WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        mysqli_stmt_bind_result($stmt, $filename, $mime_type, $file_data, $downloads);
        mysqli_stmt_fetch($stmt);

        // Send file headers for download
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime_type);
        header('Content-Disposition: attachment; filename="' . basename($filename) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . strlen($file_data));

        ob_clean();
        flush();
        echo $file_data;

        // Update download count
        $newCount = $downloads + 1;
        $updateQuery = "UPDATE files SET downloads = ? WHERE id = ?";
        $updateStmt = mysqli_prepare($conn, $updateQuery);
        mysqli_stmt_bind_param($updateStmt, "ii", $newCount, $id);
        mysqli_stmt_execute($updateStmt);
        mysqli_stmt_close($updateStmt);

        exit;
    } else {
        echo "<script>alert('File not found!'); window.location='index.php';</script>";
    }
    mysqli_stmt_close($stmt);
}
?>
