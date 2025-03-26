<?php
$conn = mysqli_connect('localhost', 'root', '', 'navsclubs');

if (!$conn) {
    die("Connection failed: " . mysqli_connect_error());
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['delete_id'])) {
    $id = intval($_POST['delete_id']);
    
    // Delete file from database
    $sql = "DELETE FROM files WHERE id = ?";
    $stmt = mysqli_prepare($conn, $sql);
    mysqli_stmt_bind_param($stmt, "i", $id);

    if (mysqli_stmt_execute($stmt)) {
        echo "success"; // Send success response
    } else {
        echo "error"; // Send error response
    }

    mysqli_stmt_close($stmt);
}

mysqli_close($conn);
?>