<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>File & Folder Upload</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
</head>
<body>
    <div class="container mt-5">
        <h3 class="text-center mb-4">Upload Files</h3>

        <div class="row justify-content-center">
            <div class="col-md-6">
                <!-- File Upload Form -->
                <form id="fileUploadForm" enctype="multipart/form-data" class="border p-4 shadow rounded bg-light mb-4">
                    <h5 class="text-center">Upload Files</h5>
                    <div class="mb-3">
                        <input type="file" id="fileInput" name="files[]" multiple class="form-control">
                    </div>
                    <button type="submit" class="btn btn-success w-100">Upload Files</button>
                </form>
                
                <div id="uploadStatus"></div>
            </div>
        </div>

        <!-- File List -->
        <h3 class="text-center mt-5">Available Files</h3>
        <table class="table table-striped mt-3">
            <thead class="table-dark">
                <tr>
                    <th>ID</th>
                    <th>Filename</th>
                    <th>Type</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody id="fileTableBody">
                <!-- Files will be loaded dynamically here -->
            </tbody>
        </table>
    </div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        $(document).ready(function() {
            function handleUpload(form, inputSelector) {
                $(form).on("submit", function(e) {
                    e.preventDefault();
                    let formData = new FormData(this);
                    let files = $(inputSelector)[0].files;
                    let allowedExtensions = ["pdf", "jpg", "jpeg", "png", "gif", "zip"];
                    let maxFileSize = 2 * 1024 * 1024; // 2MB limit
                    let errorMessage = "";

                    if (files.length === 0) {
                        $("#uploadStatus").html('<div class="alert alert-danger">No file selected!</div>');
                        return;
                    }

                    for (let i = 0; i < files.length; i++) {
                        let fileType = files[i].name.split('.').pop().toLowerCase();
                        if (!allowedExtensions.includes(fileType)) {
                            errorMessage += "Invalid file type: " + files[i].name + " (Allowed: PDF, Image, ZIP)<br>";
                        }
                        if (files[i].size > maxFileSize) {
                            errorMessage += "File too large: " + files[i].name + " (Max: 2MB)<br>";
                        }
                    }

                    if (errorMessage) {
                        $("#uploadStatus").html('<div class="alert alert-danger">' + errorMessage + '</div>');
                        return;
                    }

                    $.ajax({
                        url: "UploadFunctionMaritime.php",
                        type: "POST",
                        data: formData,
                        contentType: false,
                        processData: false,
                        beforeSend: function() {
                            $("#uploadStatus").html('<div class="alert alert-info">Uploading...</div>');
                        },
                        success: function(response) {
                            response = response.trim();
                            if (response === "success") {
                                $("#uploadStatus").html('<div class="alert alert-success">File uploaded successfully!</div>');
                                $(form)[0].reset();
                                loadFileList();
                            } else {
                                $("#uploadStatus").html('<div class="alert alert-danger">' + response + '</div>');
                            }
                        },
                        error: function() {
                            $("#uploadStatus").html('<div class="alert alert-danger">Error processing request.</div>');
                        }
                    });
                });
            }

            handleUpload("#fileUploadForm", "#fileInput");

            function loadFileList() {
    $.ajax({
        url: 'FetchMaritimeMaterials.php',
        type: 'GET',
        dataType: 'json', // Ensure JSON response is correctly parsed
        success: function(response) {
            console.log("Server Response:", response); // Debugging

            if (response.files && response.files.length > 0) {
                let tableContent = "";
                response.files.forEach((file, index) => {
                    tableContent += `
                        <tr>
                            <td>${index + 1}</td>
                            <td>${file.name}</td>
                            <td>${file.type}</td>
                            <td>
                                <button class="btn btn-danger delete-btn" data-id="${file.id}">Delete</button>
                            </td>
                        </tr>
                    `;
                });

                $('#fileTableBody').html(tableContent);
                attachDeleteEvents();
            } else {
                $('#fileTableBody').html('<tr><td colspan="4" class="text-center">No files available.</td></tr>');
            }
        },
        error: function(xhr, status, error) {
            console.error("Error fetching files:", error);
            $("#uploadStatus").html('<div class="alert alert-danger">Error fetching file list.</div>');
        }
    });
}

            function attachDeleteEvents() {
                $(".delete-btn").off("click").on("click", function() {
                    const fileId = $(this).attr("data-id");

                    if (confirm("Are you sure you want to delete this file?")) {
                        $.ajax({
                            url: "DeleteMaritimeCourse.php",
                            type: "POST",
                            data: { delete_id: fileId },
                            success: function(response) {
                                response = response.trim();
                                if (response === "success") {
                                    $("#uploadStatus").html('<div class="alert alert-success">File deleted successfully!</div>');
                                    loadFileList();
                                } else {
                                    $("#uploadStatus").html('<div class="alert alert-danger">Error deleting file!</div>');
                                }
                            },
                            error: function() {
                                $("#uploadStatus").html('<div class="alert alert-danger">Error processing delete request.</div>');
                            }
                        });
                    }
                });
            }

            loadFileList();
        });
    </script>
</body>
</html>
