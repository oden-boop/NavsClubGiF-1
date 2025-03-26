<?php
include_once("newincludes/newconfig.php");

$l_name = '';
$l_des = '';

if (isset($_REQUEST['lecSubmitBtn'])) {
    $l_name = $_REQUEST['lec_name'];
    $l_des = $_REQUEST['lec_design'];
    
    if (empty($l_name) || empty($l_des)) {
        $msg = '<div class="alert alert-warning col-sm-6 ml-5 mt-2">All Fields Required</div>';
    } else {
        $lec_name = $_REQUEST['lec_name'];
        $lec_design = $_REQUEST['lec_design'];
        $lec_image = $_FILES['lec_image']['name'];
        $lec_image_temp = $_FILES['lec_image']['tmp_name'];
        $image_folder = '../Images/Lectures/' . $lec_image;
        move_uploaded_file($lec_image_temp, $image_folder);

        $sql = "INSERT INTO course_lectures(lecture_name, lecture_design, lecture_image) VALUES ('$lec_name', '$lec_design', '$image_folder')";

        if ($conn->query($sql) === TRUE) {
            $msg = '<div class="alert alert-success col-sm-6 ml-5 mt-2">Lecture Added Successfully</div>';
            echo "<script>setTimeout(()=>{window.location.href='CreateLecturesFunction.php';},300);</script>";
        } else {
            $msg = '<div class="alert alert-danger col-sm-6 ml-5 mt-2">Lecture Addition Failed: ' . $conn->error . '</div>';
        }
    }
}
?>

<div class="col-sm-6 mt-5 jumbotron">
    <h3 class="text-center">Add Lectures</h3>
    <form action="" method="POST" enctype="multipart/form-data">
        <br>
        <?php if (isset($msg)) { echo $msg; } ?><br>
        <div class="form-group">
            <label for="lec_name">Lecture Name</label>
            <input type="text" id="lec_name" name="lec_name" class="form-control" value="<?php echo $l_name; ?>">
        </div><br>
        <div class="form-group">
            <label for="lec_design">Lecture Designation</label>
            <input type="text" id="lec_design" name="lec_design" class="form-control" value="<?php echo $l_des; ?>">
        </div><br>
        <div class="form-group">
            <label for="lec_image">Lecture Image</label>
            <input type="file" id="lec_image" name="lec_image" class="form-control-file">
        </div><br>
        <div class="text-center"> 
            <button class="btn btn-danger" type="submit" id="lecSubmitBtn" name="lecSubmitBtn">Submit</button>
            <a href="Lectures.php" class="btn btn-secondary">Close</a>
        </div>
    </form>
</div>