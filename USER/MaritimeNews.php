<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Maritime News</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: 30px auto;
            background: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .news-item {
            border-bottom: 1px solid #ddd;
            padding: 15px 0;
        }
        .news-item:last-child {
            border-bottom: none;
        }
        .news-title {
            font-size: 18px;
            font-weight: bold;
            color: #007bff;
        }
        .news-description {
            font-size: 14px;
            color: #333;
        }
        .news-link {
            font-size: 14px;
            color: #28a745;
            text-decoration: none;
            display: block;
            margin-top: 5px;
        }
        .news-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>

    <div class="container">
        <h2 class="text-center mb-4">Latest Maritime News</h2>
        <div id="news-container"></div>
    </div>

    <script>
        document.addEventListener("DOMContentLoaded", function () {
            fetch("get_news.php?q=maritime")
                .then(response => response.json())
                .then(data => {
                    console.log("API Response:", data);
                    if (data.status === "ok") {
                        displayNews(data.articles);
                    } else {
                        document.getElementById("news-container").innerHTML = "<p class='text-danger'>Error fetching news.</p>";
                    }
                })
                .catch(error => console.error("Fetch error:", error));
        });

        function displayNews(articles) {
            let newsContainer = document.getElementById("news-container");
            newsContainer.innerHTML = "";

            articles.forEach(article => {
                let newsItem = document.createElement("div");
                newsItem.classList.add("news-item");
                newsItem.innerHTML = `
                    <h3 class="news-title">${article.title}</h3>
                    <p class="news-description">${article.description || "No description available"}</p>
                    <a href="${article.url}" target="_blank" class="news-link">Read More</a>
                `;
                newsContainer.appendChild(newsItem);
            });
        }
    </script>

</body>
</html>
