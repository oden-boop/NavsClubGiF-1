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

<section id="course-inner">
    <div class="overview">
        <img class="course-img" 
             src="<?= isset($course['course_image']) ? str_replace('..', '.', $course['course_image']) : 'default.jpg'; ?>" 
             alt="Course Image">
        <div class="course-head">
            <div class="c-name">
                <h3><?= htmlspecialchars($course['course_name'] ?? ''); ?></h3>
            </div>
            <span class="price">&#36;<?= htmlspecialchars($course['course_price'] ?? ''); ?></span>
        </div>
        <h3>Instructor Name</h3>
        <div class="tutor">
            <div class="tutor-dt">
                <p><?= htmlspecialchars($course['course_instructor'] ?? ''); ?></p>
            </div>
        </div>
        <h3>Description</h3>
        <p class="description"><?= htmlspecialchars($course['course_desc'] ?? ''); ?></p>

        <!-- Add to Cart Button -->
        <button id="addToCart" class="btn btn-primary"
                data-course-id="<?= $cid; ?>">
            Add to Cart
        </button>
        <p id="cartMessage" style="color: green; font-weight: bold; margin-top: 10px;"></p>
    </div>
</section>

<table>
    <thead>
        <th scope="col">Lesson No.</th>
        <th scope="col">Lesson Name</th>
    </thead>
    <tbody>
        <?php
        $sql = "SELECT * FROM course_lessons WHERE course_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $cid);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $num = 0;
            while ($row = $result->fetch_assoc()) {
                $num++;
                echo "<tr class='tr'>
                    <th scope='row'>{$num}</th>
                    <td>" . htmlspecialchars($row['lesson_name']) . "</td>
                </tr>";
            }
        }
        ?>
    </tbody>
</table>

<style>
    table {
        width: 50%;
        margin-left: auto;
        margin-right: auto;
        font-size: 1.3rem;
        margin-top: -50px;
    }
    thead, td {
        text-align: center;
        padding: 10px;
    }
</style>

<script>
document.getElementById("addToCart").addEventListener("click", function () {
    const courseId = this.getAttribute("data-course-id");

    console.log("Sending course_id:", courseId); // Debugging

    fetch('FetchAddtoCartCourse.php', {
        method: "POST",
        headers: { "Content-Type": "application/x-www-form-urlencoded" },
        body: "course_id=" + encodeURIComponent(courseId)
    })
    .then(response => response.text()) // Get raw response
    .then(data => {
        console.log("Response from server:", data); // Debugging

        try {
            let json = JSON.parse(data);
            if (json.status === "success") {
                window.location.href = "ForCheckoutCourse.php"; 
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
