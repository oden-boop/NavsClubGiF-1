<?php
include_once("newincludes/newconfig.php");

if (isset($_REQUEST['delete'])) {
    $sql = "DELETE FROM lessons WHERE lesson_id={$_REQUEST['id']}";
    if ($conn->query($sql) === TRUE) {
        echo '<meta http-equiv="refresh" content="0;URL=?deleted"/>';
    } else {
        echo "Delete Failed";
    }
}

// Fetch all lessons
$sql = "SELECT * FROM course_lessons";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Process each lesson
        $lesson_id = $row['lesson_id'];
        $lesson_name = $row['lesson_name'];
        // Add your processing logic here
    }
} else {
    echo '<div class="alert alert-dark mt-4" role="alert">No Lessons Found!</div>';
}
?>