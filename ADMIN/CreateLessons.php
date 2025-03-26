<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Sections - Dummy Data</title>
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
        }
        .section-card:hover {
            box-shadow: 0 8px 16px rgba(0, 0, 0, 0.2);
        }
        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            cursor: pointer;
            transition: 0.3s;
        }
        .section-header:hover {
            color: #007bff;
        }
        .plus-icon {
            font-size: 24px;
            cursor: pointer;
        }
        .collapse {
            transition: all 0.3s ease-in-out;
        }
    </style>
</head>
<body>

<div class="container">
    <h2 class="text-center">Course Sections (Dummy Data)</h2>
    
    <!-- Dummy Section 1 -->
    <div class="card section-card">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#section1">
            <h4>Course Name: HTML Basics (Position: 1)</h4>
            <span class="plus-icon">➕</span>
        </div>
        <div id="section1" class="collapse">
            <div class="card-body">
                <p><strong>VdoCipher Video ID:</strong> xyz123abc</p>
                <button class="btn btn-primary btn-sm">Edit</button>
                <button class="btn btn-danger btn-sm">Delete</button>
            </div>
        </div>
    </div>

    <!-- Dummy Section 2 -->
    <div class="card section-card">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#section2">
            <h4>Course Name: CSS Fundamentals (Position: 2)</h4>
            <span class="plus-icon">➕</span>
        </div>
        <div id="section2" class="collapse">
            <div class="card-body">
                <p><strong>VdoCipher Video ID:</strong> abc456def</p>
                <button class="btn btn-primary btn-sm">Edit</button>
                <button class="btn btn-danger btn-sm">Delete</button>
            </div>
        </div>
    </div>

    <!-- Dummy Section 3 -->
    <div class="card section-card">
        <div class="section-header" data-bs-toggle="collapse" data-bs-target="#section3">
            <h4>Course Name: JavaScript Essentials (Position: 3)</h4>
            <span class="plus-icon">➕</span>
        </div>
        <div id="section3" class="collapse">
            <div class="card-body">
                <p><strong>VdoCipher Video ID:</strong> lmn789ghi</p>
                <button class="btn btn-primary btn-sm">Edit</button>
                <button class="btn btn-danger btn-sm">Delete</button>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
