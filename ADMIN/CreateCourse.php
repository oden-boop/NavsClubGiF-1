<?php
ini_set('log_errors', 1);
ini_set('error_log', 'errors.log');
error_reporting(E_ALL);
ini_set('display_errors', 0);

include_once("newincludes/newconfig.php");
include_once("CreateCourseFunction.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['courseSubmitBtn'])) {
        $msg = insertCourse($conn);
    }

    if (isset($_POST['delete']) && isset($_POST['id'])) {
        $course_id = intval($_POST['id']);
        deleteCourse($conn, $course_id);
    }
}

$result = fetchCourses($conn);
?>

<div class="container mt-4">
    <h3 class="text-center text-dark">Course Management</h3>
    <hr class="mb-4">

    <?php if ($result && $result->num_rows > 0) { ?>
        <table class="table table-bordered text-center">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Level</th>
                    <th>Price</th>
                    <th>Thumbnail</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()) { ?>
                    <tr>
                        <td><?= htmlspecialchars($row['course_id']); ?></td>
                        <td><?= htmlspecialchars($row['course_name']); ?></td>
                        <td><?= htmlspecialchars($row['course_level']); ?></td>
                        <td>$<?= htmlspecialchars($row['course_price']); ?></td>
                        <td>
                            <img src="<?= htmlspecialchars($row['course_image']); ?>" alt="Thumbnail" width="60" class="img-thumbnail">
                        </td>
                        <td>
                            <!-- ✅ Send course_id & course_name -->
                            <form action="CreateLessons.php" method="GET" class="d-inline">
                                <input type="hidden" name="course_id" value="<?= htmlspecialchars($row['course_id']); ?>">
                                <input type="hidden" name="course_name" value="<?= htmlspecialchars($row['course_name']); ?>">
                                <button type="submit" class="btn btn-success btn-sm">
                                    <i class="uil uil-eye"></i> View
                                </button>
                            </form>

                            <form action="" method="POST" class="d-inline">
                                <input type="hidden" name="id" value="<?= htmlspecialchars($row['course_id']); ?>">
                                <button type="submit" class="btn btn-danger btn-sm" name="delete">
                                    <i class="uil uil-trash-alt"></i> Delete
                                </button>
                            </form>
                        </td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>
    <?php } else { ?>
        <p class="text-muted text-center">No courses found.</p>
    <?php } ?>
</div>


<div class="container mt-5">
    <div class="card shadow p-4">
        <h4 class="text-center text-primary">Add New Course</h4>
        <form action="" method="POST" enctype="multipart/form-data" id="courseForm">
            <?php if (isset($msg)) echo $msg; ?>

            <div class="mb-3">
                <label class="form-label">Course Name</label>
                <input type="text" id="course_name" name="course_name" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Course Description</label>
                <textarea id="course_desc" name="course_desc" class="form-control" required></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Course Price</label>
                <input type="number" id="course_price" name="course_price" class="form-control" min="0" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Level</label>
                <select class="form-select" name="course_level" id="course_level" required>
                    <option value="Beginner">Beginner</option>
                    <option value="Intermediate">Intermediate</option>
                    <option value="Advanced">Advanced</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="form-label">Thumbnail</label>
                <input type="file" id="course_img" name="course_img" class="form-control" accept=".jpg, .jpeg, .png, .gif" required>
            </div>

            <div class="text-center">
                <button class="btn btn-primary btn-sm" type="submit" id="courseSubmitBtn" name="courseSubmitBtn">Submit</button>
                <a href="Course.php" class="btn btn-secondary btn-sm">Close</a>
            </div>
        </form>
    </div>
</div>
