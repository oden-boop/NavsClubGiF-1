
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
            color: #007bff;
            cursor: pointer;
            transition: 0.3s;
        }
        .btn-add-lesson:hover {
            color: #0056b3;
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

<!-- ✅ Modal -->
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
                        <input 
                            type="number" 
                            id="sectionPosition" 
                            name="section_position" 
                            class="form-control" 
                            placeholder="Enter position" 
                            min="1" 
                            required
                        >
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
<!-- ✅ Lesson Modal -->
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

                    <div class="mb-3">
                        <label class="form-label">Position</label>
                        <input type="text" id="position" class="form-control" readonly>
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
                        <textarea id="lessonDescription" name="lesson_description" class="form-control" placeholder="Enter description" required></textarea>
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



<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<script>
$(document).ready(function () {
    // ✅ Load sections on page load
    loadSections();

    // ✅ Form submission handler for adding sections
    $('#addSectionForm').submit(function (e) {
        e.preventDefault();

        $.ajax({
            url: 'CreateLessonsFunction.php',
            type: 'POST',
            data: $(this).serialize(),
            success: function (response) {
                alert(response);
                $('#addSectionModal').modal('hide');
                loadSections();
            },
            error: function () {
                alert('❌ Failed to add section');
            }
        });
    });

    // ✅ Function to load sections dynamically with course_id from URL
    function loadSections() {
        const params = new URLSearchParams(window.location.search);
        const courseId = params.get('course_id');
        const courseName = params.get('course_name');

        if (!courseId || !courseName) {
            console.error('❌ Missing course ID or name');
            alert('❌ Missing course ID or course name');
            return;
        }


        $.ajax({
            url: 'fetchCourse_sections.php',
            type: 'GET',
            data: { course_id: courseId },
            dataType: 'json',
            success: function (data) {
                let html = ``;

                if (data.length > 0) {
                    data.forEach(function (section) {
                        html += `
                            <div class="section-card" data-section-id="${section.section_id}">
                                <div class="section-header" onclick="toggleSection(this)">
                                    <h4>${section.section_name} (Position: ${section.position})</h4>
                                    <span class="arrow">▼</span>
                                </div>
                                
                                <!-- ✅ Lesson Container -->
                                <div class="lesson-container" id="lesson-container-${section.section_id}">
                                    <div class="btn-add-lesson" 
                                         onclick="openLessonModal('${courseName}', 
                                                                  ${section.section_id}, 
                                                                  ${courseId}, 
                                                                  '${section.section_name}', 
                                                                  ${section.position})">
                                        ➕ Add Lesson
                                    </div>
                                    <p>Lesson content goes here...</p>
                                </div>
                            </div>
                        `;

                        // ✅ Auto-load lessons for each section
                        loadLessons(section.section_id, courseId, courseName, section.section_name, section.position);
                    });
                } else {
                    html += '<p>No sections found.</p>';
                }

                $('#sectionsContainer').html(html);
            },
            error: function () {
                alert('❌ Failed to load sections');
            }
        });
    }

    // ✅ Function to load lessons dynamically
    function loadLessons(sectionId, courseId, courseName, sectionName, position) {
        $.ajax({
            url: 'fetchLessonsWSection.php',
            type: 'GET',
            data: { section_id: sectionId, course_id: courseId },  // Include course_id
            dataType: 'json',
            success: function (lessons) {
                let html = '';

                if (lessons.length > 0) {
                    lessons.forEach(lesson => {
                        html += `
                            <div class="lesson-card">
                                <h5>📚 ${lesson.lesson_name || 'Untitled Lesson'}</h5>
                                <p>🎥 Video ID: ${lesson.video_id || '-'}</p>
                                <p>📚 Section: ${sectionName} | Position: ${position}</p>
                                <p>🎓 Course: ${courseName}</p>
                            </div>`;
                    });
                } else {
                    // ✅ Show "➕ Add Lesson" button only if no lessons found
                    html = `
                        <div class="btn-add-lesson" 
                             onclick="openLessonModal('${courseName}', 
                                                      ${sectionId}, 
                                                      ${courseId}, 
                                                      '${sectionName}', 
                                                      ${position})">
                            ➕ Add Lesson
                        </div>
                        <p>No lessons found.</p>`;
                }

                $(`#lesson-container-${sectionId}`).html(html);
            },
            error: function (xhr) {
                console.error('❌ Error:', xhr.responseText);
                alert(`❌ Failed to load lessons for Section ${sectionId}`);
            }
        });
    }

    // ✅ Expand/1lapse Section (Dropdown Logic)
    window.toggleSection = function (el) {
        const card = $(el).closest('.section-card');
        const lessonContainer = card.find('.lesson-container');

        lessonContainer.slideToggle(300);   // Toggle lesson-container visibility
        card.toggleClass('collapsed');
    }

    // ✅ Open Lesson Modal with Course and Section Info
    window.openLessonModal = function (courseName, sectionId, courseId, sectionName, position) {
        console.log('Course ID:', courseId);
        console.log('Course Name:', courseName);
        console.log('Section ID:', sectionId);
        console.log('Section Name:', sectionName);
        console.log('Position:', position);

        // ✅ Use consistent modal field names
        $('#courseIdModal').val(courseId);
        $('#courseNameModal').val(courseName);
        $('#sectionId').val(sectionId);
        $('#sectionNameModal').val(sectionName);
        $('#position').val(position);

        $('#lessonModal').modal('show');
    }

    // ✅ Form submission handler for adding lessons
    $('#addLessonForm').submit(function (e) {
        e.preventDefault();

        const sectionId = $('#sectionId').val();
        const courseId = $('#courseIdModal').val();
        const courseName = $('#courseNameModal').val();
        const sectionName = $('#sectionNameModal').val();
        const position = $('#position').val();

        const formData = $(this).serialize() + `&section_id=${sectionId}&course_id=${courseId}`;

        $.ajax({
            url: 'FetchSection_course.php',
            type: 'POST',
            data: formData,
            success: function (response) {
                alert(response);
                $('#lessonModal').modal('hide');
                loadLessons(sectionId, courseId, courseName, sectionName, position);  // Reload lessons
            },
            error: function () {
                alert('❌ Failed to add lesson');
            }
        });
    });
});


</script>

</body>
</html>