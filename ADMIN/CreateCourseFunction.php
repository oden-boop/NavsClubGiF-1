<?php
include_once("newincludes/newconfig.php");

function insertCourse($conn) {
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['courseSubmitBtn'])) {
        $course_name = mysqli_real_escape_string($conn, $_POST['course_name']);
        $course_desc = mysqli_real_escape_string($conn, $_POST['course_desc']);
        $course_price = floatval($_POST['course_price']);
        $course_level = mysqli_real_escape_string($conn, $_POST['course_level']);
        
        // Handle Image Upload
        if (isset($_FILES['course_image']) && $_FILES['course_image']['error'] === 0) {
            $targetDir = "uploads/"; // Make sure this folder exists
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0777, true);
            }

            $fileName = time() . "_" . basename($_FILES['course_image']['name']);
            $targetFilePath = $targetDir . $fileName;
            $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

            // Allowed file types
            $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];
            if (in_array($fileType, $allowedTypes)) {
                if (move_uploaded_file($_FILES['course_image']['tmp_name'], $targetFilePath)) {
                    $course_image = $targetFilePath;
                } else {
                    return "Error uploading image.";
                }
            } else {
                return "Invalid image format. Allowed: JPG, JPEG, PNG, GIF.";
            }
        } else {
            $course_image = "uploads/default.jpg"; // Default image if none uploaded
        }

        // Insert into database
        $query = "INSERT INTO courses (course_name, course_desc, course_price, course_level, course_image) 
                  VALUES ('$course_name', '$course_desc', '$course_price', '$course_level', '$course_image')";

        if (mysqli_query($conn, $query)) {
            header("Location: " . $_SERVER['PHP_SELF']);
            exit();
        } else {
            return "Error: " . mysqli_error($conn);
        }
    }
}

function fetchCourses($conn) {
    $query = "SELECT * FROM courses ORDER BY course_id DESC";
    return mysqli_query($conn, $query);
}

function deleteCourse($conn, $course_id) {
    $query = "DELETE FROM courses WHERE course_id = $course_id";
    if (mysqli_query($conn, $query)) {
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    } else {
        return "Error deleting course: " . mysqli_error($conn);
    }
}
?>
