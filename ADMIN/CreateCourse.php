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

<div class="container mt-5">
    <h3 class="text-center text-primary fw-bold">Course Management</h3>
    <hr class="mb-4 border-primary">

    <div class="text-end mb-3">
        <button class="btn btn-primary shadow-lg rounded-pill px-4" data-bs-toggle="modal" data-bs-target="#addCourseModal">
            <i class="uil uil-plus-circle"></i> Add Course
        </button>
    </div>

    <?php if ($result && $result->num_rows > 0) { ?>
        <div class="table-responsive">
            <table class="table table-striped table-hover text-center shadow-lg rounded-3 bg-white border border-primary">
                <thead class="bg-primary text-white">
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
                            <td class="text-success fw-bold">$<?= htmlspecialchars($row['course_price']); ?></td>
                            <td>
                                <img src="<?= htmlspecialchars($row['course_image']); ?>" alt="Thumbnail" width="60" class="rounded shadow-sm border border-primary">
                            </td>
                            <td>
                                <form action="CreateLessons.php" method="GET" class="d-inline">
                                    <input type="hidden" name="course_id" value="<?= htmlspecialchars($row['course_id']); ?>">
                                    <button type="submit" class="btn btn-primary btn-sm shadow rounded-pill px-3">
                                        <i class="uil uil-eye"></i> View
                                    </button>
                                </form>

                                <form action="" method="POST" class="d-inline">
                                    <input type="hidden" name="id" value="<?= htmlspecialchars($row['course_id']); ?>">
                                    <button type="submit" class="btn btn-danger btn-sm shadow rounded-pill px-3" name="delete">
                                        <i class="uil uil-trash-alt"></i> Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } else { ?>
        <p class="text-muted text-center">No courses found.</p>
    <?php } ?>
</div>

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1" aria-labelledby="addCourseModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content shadow-lg rounded-4 border border-primary">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body bg-white">
                <form action="" method="POST" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label class="form-label">Course Name</label>
                        <input type="text" name="course_name" class="form-control rounded-3 border-primary" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course Description</label>
                        <textarea name="course_desc" class="form-control rounded-3 border-primary" required></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course Price</label>
                        <input type="number" name="course_price" class="form-control rounded-3 border-primary" min="0" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Level</label>
                        <select name="course_level" class="form-select rounded-3 border-primary" required>
                            <option value="Beginner">Beginner</option>
                            <option value="Intermediate">Intermediate</option>
                            <option value="Advanced">Advanced</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Course Image</label>
                        <input type="file" name="course_image" class="form-control rounded-3 border-primary" accept="image/*">
                    </div>
                    <div class="text-center">
                        <button class="btn btn-primary btn-sm shadow-lg rounded-pill px-4" type="submit" name="courseSubmitBtn">Submit</button>
                        <button type="button" class="btn btn-secondary btn-sm shadow-lg rounded-pill px-4" data-bs-dismiss="modal">Close</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
<!-- Bootstrap 5 CSS -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<!-- Bootstrap Icons (Optional) -->
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

<!-- Bootstrap 5 JavaScript (for modals, tooltips, etc.) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
