<?php
// ✅ Check if required parameters are provided
if (!isset($_GET['course_id']) || !isset($_GET['video_id']) || !isset($_GET['lessons'])) {
    die("❌ Invalid request. Required parameters are missing.");
}

// ✅ Assign sanitized values
$course_id = htmlspecialchars($_GET['course_id']);
$video_id = htmlspecialchars($_GET['video_id']);
$lessons = htmlspecialchars($_GET['lessons']);

// ✅ Debugging output
echo "<p>Debug: Video ID = <strong>$video_id</strong></p>";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Course Video</title>
    <style>
        .video-container {
            width: 80%;
            margin: 0 auto;
            text-align: center;
        }
        iframe {
            width: 100%;
            height: 500px;
        }
    </style>
</head>
<body>
    <div class="video-container">
        <h2>Course ID: <?= $course_id; ?></h2>
        <h4>Total Lessons: <?= $lessons; ?></h4>

        <!-- ✅ Embed VdoCipher Player -->
        <div style="padding-top:56%; position:relative;">
            <iframe id="vdoPlayer" 
                src="" 
                style="border:0;max-width:100%;position:absolute;top:0;left:0;height:100%;width:100%;" 
                allowFullScreen="true" allow="encrypted-media">
            </iframe>
        </div>
    </div>

    <!-- ✅ JavaScript to Fetch OTP & Load Video -->
    <script>

        document.addEventListener("DOMContentLoaded", async function () {
    // Get video ID from PHP safely
    const videoId = <?= json_encode($video_id ?? "", JSON_HEX_TAG); ?>;
    console.log("🔍 Debug: JavaScript received videoId =", videoId);

    // Validate the video ID
    if (!videoId || videoId === "null" || videoId === "undefined" || videoId.trim() === "") {
        console.error("❌ Error: Video ID is empty or invalid.");
        alert("❌ Video ID is missing. Check the URL.");
        return;
    }

    try {
        // ✅ Fetch OTP from PHP script (force fresh request with cache buster)
        const response = await fetch(`fetch_vdocipher_details.php?video_id=${encodeURIComponent(videoId)}&_=${Date.now()}`);
        const data = await response.json();

        if (!data || data.error) {
            console.error("❌ Server Error:", data);
            alert("❌ Error fetching video: " + (data.error || "Unknown error"));
            return;
        }

        console.log("✅ OTP Response:", data);

        // Construct playback URL safely
        const otp = data.otp;
        const playbackInfo = JSON.stringify({ videoId });

        document.getElementById("vdoPlayer").src =
            `https://player.vdocipher.com/v2/?otp=${encodeURIComponent(otp)}&playbackInfo=${encodeURIComponent(btoa(playbackInfo))}`;

    } catch (error) {
        console.error("❌ Fetch Error:", error);
        alert("❌ Failed to load video. Check the console.");
    }
});

    </script>
</body>
</html>
