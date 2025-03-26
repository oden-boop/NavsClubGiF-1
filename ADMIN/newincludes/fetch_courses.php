<?php
include_once("newconfig.php");

// ✅ Fetch All Courses
function fetchCourses($conn) {
    $sql = "SELECT * FROM courses";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        while ($course = $result->fetch_assoc()) {
            echo '<option value="' . $course['course_id'] . '">' . $course['course_id'] . ' - ' . $course['course_name'] . '</option>';
        }
    } else {
        echo '<option value="" disabled>No Courses Found</option>';
    }
}
?>
