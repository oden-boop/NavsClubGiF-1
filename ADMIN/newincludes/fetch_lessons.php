<?php
include_once("newconfig.php");

// ✅ Fetch All Lessons
function fetchLessons($conn) {
    $sql = "SELECT * FROM course_lessons";
    return $conn->query($sql);
}
?>
