<?php
include_once("fetch_lessons.php");

function displayLessonsTable($conn) {
    $lessons_result = fetchLessons($conn);

    if ($lessons_result->num_rows > 0) {
        echo '<div class="col-sm-9 mt-5">';
        echo '<h5 class="mt-5 bg-dark text-white p-2">List of Lessons</h5>';
        echo '<table class="table">';
        echo '<thead><tr><th>Lesson ID</th><th>Lesson Name</th><th>Lesson Image</th><th>Action</th></tr></thead>';
        echo '<tbody>';
        while ($row = $lessons_result->fetch_assoc()) {
            echo '<tr>';
            echo '<td>' . $row['lesson_id'] . '</td>';
            echo '<td>' . $row['lesson_name'] . '</td>';
            echo '<td><img src="' . $row['lesson_image'] . '" alt="Lesson Image" class="img-thumbnail" style="max-width: 100px;"></td>';
            echo '<td>';
            echo '<form action="editLesson.php" method="POST" class="d-inline">';
            echo '<input type="hidden" name="id" value=' . $row["lesson_id"] . '>';
            echo '<button type="submit" class="btn btn-info mr-3" name="view" value="View"><i class="uil uil-pen"></i></button>';
            echo '</form>';
            echo '<form action="" method="POST" class="d-inline">';
            echo '<input type="hidden" name="id" value=' . $row["lesson_id"] . '>';
            echo '<button type="submit" class="btn btn-secondary" name="delete" value="Delete">';
            echo '<i class="uil uil-trash-alt"></i>';
            echo '</button>';
            echo '</form>';
            echo '</td>';
            echo '</tr>';
        }
        echo '</tbody></table></div>';
    } else {
        echo '<div class="alert alert-dark mt-4" role="alert">No Lessons Found!</div>';
    }
}
?>
