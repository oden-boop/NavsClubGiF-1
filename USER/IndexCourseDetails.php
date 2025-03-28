<?php
// Start session

// Include necessary files
include_once("includes/config.php");

// Validate and fetch course_id
$cid = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;

if ($cid <= 0) {
    die("Invalid course ID."); // Prevent errors
}

// Store course_id in session
$_SESSION["course_id"] = $cid;

// Fetch course details safely
$sql = "SELECT * FROM courses WHERE course_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $cid);
$stmt->execute();
$result = $stmt->get_result();
$course = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Course Details</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <div class="row">
           
            <div class="col-md-6">
                <h2 class="fw-bold"> <?= htmlspecialchars($course['course_name'] ?? ''); ?> </h2>
                <h4 class="text-muted">Instructor: <?= htmlspecialchars($course['course_instructor'] ?? ''); ?></h4>
                <h5 class="text-success mt-3">Price: &#36;<?= htmlspecialchars($course['course_price'] ?? ''); ?></h5>
                <p class="mt-3"> <?= htmlspecialchars($course['course_desc'] ?? ''); ?> </p>
                <button id="addToCart" class="btn btn-primary w-100 mt-3" data-course-id="<?= $cid; ?>">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
                <p id="cartMessage" class="text-success fw-bold mt-2"></p>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.getElementById("addToCart").addEventListener("click", function () {
        const courseId = this.getAttribute("data-course-id");

        fetch('Add_toCartFunc.php', {
            method: "POST",
            headers: { "Content-Type": "application/x-www-form-urlencoded" },
            body: "course_id=" + encodeURIComponent(courseId)
        })
        .then(response => response.text())
        .then(data => {
            try {
                let json = JSON.parse(data);
                if (json.status === "success") {
                    window.location.href = "CourseAddedtoCart.php"; 
                } else {
                    document.getElementById("cartMessage").innerText = json.message;
                }
            } catch (error) {
                console.error("Invalid JSON Response:", data);
            }
        })
        .catch(error => console.error('Fetch Error:', error));
    });
    </script>
</body>
</html>
