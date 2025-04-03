<?php
session_start();
include_once("includes/config.php");

// Validate and fetch course_id
$cid = isset($_GET['course_id']) ? intval($_GET['course_id']) : 0;
if ($cid <= 0) {
    die("Invalid course ID.");
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
                <h2 class="fw-bold"><?= htmlspecialchars($course['course_name'] ?? ''); ?></h2>
                <h5 class="text-success mt-3">Price: &#36;<?= htmlspecialchars($course['course_price'] ?? ''); ?></h5>
                <p class="mt-3"><?= htmlspecialchars($course['course_desc'] ?? ''); ?></p>
                
                <!-- ✅ Button with correct data attributes -->
                <button id="addToCart" class="btn btn-primary w-100 mt-3" 
                    data-course-id="<?= $cid; ?>" 
                    data-course-name="<?= htmlspecialchars($course['course_name'] ?? ''); ?>" 
                    data-course-price="<?= htmlspecialchars($course['course_price'] ?? ''); ?>">
                    <i class="bi bi-cart-plus"></i> Add to Cart
                </button>
                <p id="cartMessage" class="text-success fw-bold mt-2"></p>
            </div>
        </div>
    </div>

    <!-- ✅ Include jQuery & Bootstrap -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <!-- ✅ JavaScript Code -->
    <script>
        $(document).ready(function () {
            $("#addToCart").on("click", function () {
                let button = $(this); 
                let courseId = button.data("course-id");
                let courseName = button.data("course-name");
                let coursePrice = button.data("course-price");

                if (!courseId || !courseName || !coursePrice) {
                    alert("❌ Missing Course Data. Please try again.");
                    console.log("Debugging:", { courseId, courseName, coursePrice }); 
                    return;
                }

                // ✅ Disable button while processing
                button.prop("disabled", true).text("Adding...");

                $.ajax({
                    url: "Add_toCartFunc.php",
                    type: "POST",
                    data: {
                        course_id: courseId,
                        course_name: courseName,
                        course_price: coursePrice
                    },
                    dataType: "json",
                    success: function (response) {
                        console.log("Response:", response);  

                        if (response.success) {
                            window.location.href = "AddingtoCartCourse.php"; 
                        } else {
                            $("#cartMessage")
                                .text("❌ " + response.message)
                                .css({"color": "red", "font-weight": "bold"});
                        }
                    },
                    error: function (xhr, status, error) {
                        console.error("AJAX Error:", error);
                        $("#cartMessage")
                            .text("❌ Failed to process request. Please try again.")
                            .css({"color": "red", "font-weight": "bold"});
                    },
                    complete: function () {
                        button.prop("disabled", false).text("Add to Cart");
                    }
                });
            });
        });
    </script>
</body>
</html>
