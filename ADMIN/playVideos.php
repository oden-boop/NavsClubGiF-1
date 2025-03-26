<?php
// ✅ Include VdoCipher Config
include_once("newincludes/vdo_cipher_config.php");

// ✅ Get Video ID from URL
if (!isset($_GET['video_id']) || empty($_GET['video_id'])) {
    die("❌ Invalid Video ID.");
}

$video_id = htmlspecialchars($_GET['video_id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Play Video</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
</head>
<body>

    <h2>Playing Course Video</h2>
    <p id="loadingMessage" style="display: none; color: blue; font-weight: bold;">⏳ Loading video...</p>

    <iframe id="vdoPlayer" width="560" height="315" allowfullscreen></iframe>

    <script>
        async function fetchVdoCipherDetails(videoId) {
            document.getElementById("loadingMessage").style.display = "block";

            try {
                const response = await fetch(`fetch_vdocipher_details.php?video_id=${videoId}`);
                const data = await response.json();
                
                document.getElementById("loadingMessage").style.display = "none";

                if (!response.ok || data.error) {
                    alert(`❌ Error: ${data.error || "Failed to load video."}`);
                    return;
                }

                // ✅ Load video in iframe
                document.getElementById("vdoPlayer").src = `https://player.vdocipher.com/v2/?otp=${data.otp}&playbackInfo=${data.playbackInfo}`;

            } catch (error) {
                console.error('❌ Failed to fetch VdoCipher details:', error);
                alert('❌ Failed to load video. Please try again.');
                document.getElementById("loadingMessage").style.display = "none";
            }
        }

        // ✅ Auto-fetch when page loads
        const urlParams = new URLSearchParams(window.location.search);
        const videoId = urlParams.get("video_id");
        if (videoId) fetchVdoCipherDetails(videoId);
    </script>

</body>
</html>
