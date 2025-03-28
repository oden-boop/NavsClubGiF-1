
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Sections with Modal</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f4;
            font-family: Arial, sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .section-card {
            background: #ffffff;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            transition: 0.3s;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            cursor: pointer;
        }

        .section-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .arrow {
            font-size: 18px;
            transition: transform 0.3s;
        }

        .collapsed .arrow {
            transform: rotate(-90deg);
        }

        .lesson-container {
            display: none;
            padding: 15px;
            background: #f9f9f9;
            border-radius: 8px;
            margin-top: 10px;
        }

        .btn-add-lesson {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 5px;
            color:rgb(0, 0, 0);
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-add-lesson:hover {
            color:rgb(89, 169, 255);
        }

    </style>
</head>

<body>

    <div class="container">
        <h2 class="text-center">Course Sections</h2>

        <!-- ✅ Add Section Button -->
        <button class="btn btn-primary w-100 mb-3" data-bs-toggle="modal" data-bs-target="#addSectionModal">➕ Add Section</button>

        <div id="sectionsContainer">
            <!-- Sections will be dynamically loaded here -->
        </div>
    </div>

    <!-- ✅ Modal for Adding Section -->
    <div class="modal fade" id="addSectionModal" tabindex="-1" aria-labelledby="addSectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add New Section</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="addSectionForm" method="POST">

                        <!-- Input: Section Name -->
                        <div class="mb-3">
                            <label for="sectionName" class="form-label">Section Name</label>
                            <input type="text" id="sectionName" name="section_name" class="form-control" placeholder="Enter section name" required>
                        </div>

                        <!-- Input: Position -->
                        <div class="mb-3">
                            <label for="sectionPosition" class="form-label">Position</label>
                            <input type="number" id="sectionPosition" name="section_position" class="form-control" placeholder="Enter position" min="1" required>
                        </div>

                        <!-- Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Add Section</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- ✅ Modal for Adding Lesson -->
    <div class="modal fade" id="lessonModal" tabindex="-1" aria-labelledby="lessonModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title">Add New Lesson</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <form id="addLessonForm" method="POST">

                        <!-- ✅ Hidden Inputs for IDs -->
                        <input type="hidden" id="courseIdModal" name="course_id">
                        <input type="hidden" id="sectionId" name="section_id">

                        <!-- ✅ Course Info -->
                        <div class="mb-3">
                            <label class="form-label">Course Name</label>
                            <input type="text" id="courseNameModal" name="course_name" class="form-control" readonly>
                        </div>

                        <!-- ✅ Section Info -->
                        <div class="mb-3">
                            <label class="form-label">Section Name</label>
                            <input type="text" id="sectionNameModal" class="form-control" readonly>
                        </div>

                        
                        <!-- ✅ Lesson Name -->
                        <div class="mb-3">
                            <label for="lessonName" class="form-label">Lesson Name</label>
                            <input type="text" id="lessonName" name="lesson_name" class="form-control" placeholder="Enter lesson name" required>
                        </div>

                        <!-- ✅ Video ID -->
                        <div class="mb-3">
                            <label for="videoId" class="form-label">Video ID</label>
                            <input type="text" id="videoId" name="video_id" class="form-control" placeholder="Enter video ID" required>
                        </div>

                        <!-- ✅ Description -->
                        <div class="mb-3">
                            <label for="lessonDescription" class="form-label">Description</label>
                            <textarea id="lessonDescription" name="lesson_description" class="form-control" placeholder="Enter description" readonly></textarea>
                        </div>

                        <!-- ✅ Submit Button -->
                        <div class="text-center">
                            <button type="submit" class="btn btn-primary w-100">Add Lesson</button>
                        </div>

                    </form>
                </div>

            </div>
        </div>
    </div>

    <!-- ✅ Toast Container -->
    <div id="toastContainer" class="position-fixed top-0 end-0 p-3" style="z-index: 1050;"></div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
  $(document).ready(function () {
    loadSections(); // Load sections on page load

    // ✅ Show Toast Message
    function showToast(message, type) {
        let toastHTML = `
            <div class="toast align-items-center text-white bg-${type} border-0" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                    <div class="toast-body">${message}</div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;
        $('#toastContainer').append(toastHTML);
        let toast = $('.toast').last();
        toast.toast({ delay: 3000 }).toast('show');
        setTimeout(() => toast.remove(), 3500);
    }

    // ✅ Load Sections from Database
    function loadSections() {
        $.get('FetchCreatedNewSections.php', function (data) {
            try {
                let sections = typeof data === "string" ? JSON.parse(data) : data;
                let sectionsContainer = $('#sectionsContainer');
                sectionsContainer.html(''); // Clear old content

                sections.data.forEach(section => {
                    let sectionDiv = `
                        <div class="section-card" data-section-id="${section.section_id}">
                            <div class="section-header">
                                <h4>${section.section_name} (Position: ${section.position})</h4>
                                <span class="arrow">▼</span>
                            </div>
                            <div class="lesson-container" style="display: none;"></div>
                            <button class="btn btn-primary btn-add-lesson" data-section-id="${section.section_id}">
                                + Add Lesson
                            </button>
                        </div>`;
                    sectionsContainer.append(sectionDiv);
                });
            } catch (error) {
                console.error("JSON Parsing Error: ", error, data);
            }
        }, 'json').fail(function(jqXHR, textStatus, errorThrown) {
            console.error("AJAX Error:", textStatus, errorThrown, jqXHR.responseText);
        });
    }

    // ✅ Add Section (Form Submission)
    $('#addSectionForm').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.post('CreateNewSections.php', formData, function (response) {
            if (response.success) {
                showToast(response.message, 'success');
                $('#addSectionModal').modal('hide');
                $('#addSectionForm')[0].reset();
                loadSections();
            } else {
                showToast(response.message, 'danger');
            }
        }, 'json');
    });

    // ✅ Show Add Lesson Modal
    $(document).on('click', '.btn-add-lesson', function () {
        let sectionId = $(this).data('section-id');
        let sectionName = $(this).closest('.section-card').find('.section-header h4').text();
        $('#sectionId').val(sectionId);
        $('#sectionNameModal').val(sectionName);
        $('#lessonModal').modal('show');
    });

    // ✅ Add Lesson (Form Submission)
    $('#addLessonForm').submit(function (e) {
        e.preventDefault();
        let formData = $(this).serialize();
        $.post('add_lesson.php', formData, function (response) {
            if (response.success) {
                showToast(response.message, 'success');
                $('#lessonModal').modal('hide');
                $('#addLessonForm')[0].reset();
                loadSections();
            } else {
                showToast(response.message, 'danger');
            }
        }, 'json');
    });

    // ✅ Toggle Lesson Dropdown (Fixing Click Issue)
    $(document).on('click', '.section-header', function (event) {
        event.stopPropagation(); // Prevents immediate closure
        let lessonContainer = $(this).next('.lesson-container');

        if (lessonContainer.length === 0) {
            console.error("Lesson container not found!");
            return;
        }

        // ✅ Close other open lesson containers
        $('.lesson-container').not(lessonContainer).slideUp();
        $('.section-header .arrow').not($(this).find('.arrow')).removeClass('rotate');

        // ✅ Toggle the clicked lesson container
        lessonContainer.slideToggle();
        $(this).find('.arrow').toggleClass('rotate');
    });

    // ✅ Click outside to close dropdown
    $(document).on('click', function (event) {
        if (!$(event.target).closest('.section-card').length) {
            $('.lesson-container').slideUp();
            $('.section-header .arrow').removeClass('rotate');
        }
    });

});

</script>

</body>

</html>
